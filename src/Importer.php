<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main;
use Quid\Base;

// importer
// class providing the logic to export data from an objet and importing into another one
class Importer extends Map
{
    // trait
    use _option;


    // config
    public static $config = [
        'option'=>[
            'action'=>'insert', // action par défaut
            'empty'=>false, // empty lors du trigger, booléean
            'slim'=>true, // réduit la taille du tableau de retour dans prepareReturn
            'onlyError'=>false, // le tableau de retour inclut seulement les erreurs
            'onTrigger'=>null, // callback au début du trigger
            'insert'=>null, // option pour insert
            'update'=>null, // option pour update
            'delete'=>null, // option pour delete
            'truncate'=>null, // option pour truncate
            'lineCallback'=>null] // callable pour chaque ligne
    ];


    // map
    protected static $allow = ['set','unset','empty']; // méthodes permises
    protected static $after = ['clean']; // lance la méthode clean après chaque modification


    // dynamique
    protected $source = null; // store l'instance de la source
    protected $target = null; // store la target
    protected $required = []; // store les colonnes qui ne peuvent pas être vide
    protected $callable = []; // store les callbacks pour les différentes colonnes


    // construct
    // construit l'objet importer
    public function __construct(Contract\Import $source,Contract\Import $target,?array $option=null)
    {
        $this->option($option);
        $this->setSource($source);
        $this->setTarget($target);

        return;
    }


    // setSource
    // lie l'objet source
    public function setSource(Contract\Import $value):self
    {
        $this->source = $value;

        return $this;
    }


    // source
    // retourne l'objet source
    public function source(bool $rewind=false):Contract\Import
    {
        $return = $this->source;

        if($rewind === true && !empty($return))
        $return->sourceRewind();

        return $return;
    }


    // setTarget
    // lie l'objet target
    public function setTarget(Contract\Import $value):self
    {
        $this->target = $value;

        return $this;
    }


    // target
    // retourne l'objet target
    public function target():Contract\Import
    {
        return $this->target;
    }


    // set
    // set pour importer
    // key doit être int et value doit être une string représentant un nom de colonne
    public function set($key,$value):parent
    {
        $value = Base\Obj::cast($value);

        if(!is_scalar($key))
        static::throw('keyMustBeScalar');

        if(!is_scalar($value))
        static::throw('valueMustBeScalar');

        parent::set($key,$value);

        return $this;
    }


    // setCallback
    // lie une callback à une colonne déjà set
    // envoie une exception si la clé n'existe pas
    public function setCallback($key,?callable $callable):self
    {
        $this->checkExists($key);
        $this->callable[$key] = $callable;

        return $this;
    }


    // setRequired
    // marque une colonne comme requise ou non
    // envoie une exception si la clé n'existe pas
    public function setRequired($key,bool $required=false):self
    {
        $this->checkExists($key);
        $this->required[$key] = $required;

        return $this;
    }


    // clean
    // enlève les callables et required pour toutes les clés de colonnes inexistants
    protected function clean():self
    {
        foreach ($this->callable as $key => $value)
        {
            if(!$this->exists($key))
            unset($this->callable[$key]);
        }

        foreach ($this->required as $key => $value)
        {
            if(!$this->exists($key))
            unset($this->required[$key]);
        }

        return $this;
    }


    // associate
    // lie une colonne de la source, à une colonne dans la target
    // peut aussi marquer une colonne comme requise
    // peut aussi lié une callable qui agira comme array_map
    public function associate($key,$value,bool $required=false,?callable $callable=null):self
    {
        $this->set($key,$value);
        $this->setRequired($key,$required);
        $this->setCallback($key,$callable);

        return $this;
    }


    // getMap
    // retourne un tableau avec la clé de colonne, la colonne et si présent, la callable et le statut required
    public function getMap($key):?array
    {
        $return = null;
        $value = $this->get($key);

        if(is_scalar($value))
        {
            $return[0] = $key;
            $return[1] = $value;
            $return[2] = $this->required[$key] ?? false;
            $return[3] = $this->callable[$key] ?? null;
        }

        return $return;
    }


    // getMaps
    // retourne un tableau multidimensionnel avec tous les liens colonnes et callable
    public function getMaps():array
    {
        $return = [];

        foreach ($this->arr() as $key => $value)
        {
            $return[$key] = $this->getMap($key);
        }

        return $return;
    }


    // checkMaps
    // retourne un tableau multidimensionnel avec tous les liens colonnes et callable
    // envoie une exception si vide
    public function checkMaps():array
    {
        $return = $this->getMaps();

        if(empty($return))
        static::throw('noColumnMapped');

        return $return;
    }


    // emulate
    // émule l'insertion des lignes
    public function emulate($offset=true,$length=true,?array $option=null):array
    {
        $return = ['total'=>[],'data'=>[]];
        $option = Base\Arr::plus($this->option(),$option);
        $source = $this->source(true);
        $i = 0;

        while ($line = $source->sourceOne($offset,$length,$i))
        {
            $return['data'][$i] = $this->one($line);
            $i++;
        }

        $return['total'] = $this->makeTotal($return['data']);
        $return = $this->prepareReturn($return,$option);

        return $return;
    }


    // makeTotal
    // calcul le total des lignes valides et non valides
    protected function makeTotal(array $data):array
    {
        $return = ['valid'=>0,'invalid'=>0,'save'=>0,'noSave'=>0,'insert'=>0,'update'=>0,'delete'=>0];

        foreach ($data as $key => $value)
        {
            if(is_array($value) && array_key_exists('valid',$value))
            {
                if($value['valid'] === true)
                $return['valid']++;

                else
                $return['invalid']++;

                if($value['save'] === true)
                $return['save']++;

                else
                $return['noSave']++;

                $action = $value['action'];
                if(array_key_exists($action,$return))
                $return[$action]++;
            }
        }

        return $return;
    }


    // prepareReturn
    // prépare le tableau de retour
    // si slim est true, alors enlève object et source de chaque tableau data
    protected function prepareReturn(array $return,array $option):array
    {
        $option = Base\Arr::plus($this->option(),$option);

        foreach ($return['data'] as $key => $value)
        {
            if($option['slim'] === true)
            unset($return['data'][$key]['source']);

            if($option['onlyError'] === true && $value['valid'] === true)
            unset($return['data'][$key]);
        }

        return $return;
    }


    // one
    // prépare les données d'une ligne de la source
    // callback par cellule et ensuite pour la ligne dans son entièreté
    // une ligne retournera null si un des champs requis est vide
    // si required est true, utilise la méthode base/validate isReallyEmpty
    public function one(array $value):array
    {
        $return = ['action'=>null,'valid'=>false,'error'=>null,'save'=>false,'int'=>null,'data'=>[],'source'=>$value];
        $maps = $this->checkMaps();
        $keys = array_keys($maps);

        if(Base\Arr::keysExists($keys,$value))
        {
            $line = [];

            foreach ($value as $k => $v)
            {
                if(array_key_exists($k,$maps))
                {
                    $original = $v;
                    $col = $maps[$k][1];
                    $required = $maps[$k][2] ?? false;
                    $callable = $maps[$k][3] ?? null;

                    if(static::classIsCallable($callable))
                    $v = $callable($v,$value,$line);

                    if($v === false || ($required === true && Base\Validate::isReallyEmpty($v)))
                    {
                        $return['error'] = [$col=>$original];
                        break;
                    }

                    else
                    $line[$col] = $v;
                }
            }

            $return['data'] = $line;
            if(!empty($line))
            $return = $this->oneAfter($return);
        }

        else
        static::throw('invalidLine',...array_values($value));

        return $return;
    }


    // oneAfter
    // après one, s'il y a des données à la ligne
    // détecte si valide et l'action à utiliser
    protected function oneAfter(array $return):array
    {
        if(!empty($return['data']))
        {
            ksort($return['data']);

            if(empty($return['error']))
            $return['valid'] = true;
        }

        $lineCallback = $this->getOption('lineCallback');
        if(static::classIsCallable($lineCallback))
        $return = $lineCallback($return);

        if($return['valid'] === true)
        {
            if(empty($return['action']))
            $return['action'] = $this->getOption('action');

            if($return['action'] === 'delete')
            $return['data'] = null;
        }

        return $return;
    }


    // trigger
    // prépare et insère les lignes dans la table de données
    public function trigger($offset=true,$length=true,?array $option=null):array
    {
        $return = ['total'=>[],'data'=>[]];
        $option = Base\Arr::plus($this->option(),$option);
        $target = $this->target();
        $onBefore = $option['onBefore'] ?? null;
        $empty = $option['empty'] ?? null;
        $source = $this->source(true);
        $i = 0;

        if($empty === true)
        $target->targetTruncate($option['truncate'] ?? null);

        if(static::classIsCallable($onBefore))
        $onBefore($this);

        while ($line = $source->sourceOne($offset,$length,$i))
        {
            $one = $this->one($line);

            if(!empty($one) && $one['valid'] === true)
            {
                $action = $one['action'];
                $int = $one['int'];
                $data = $one['data'];

                if($action === 'insert')
                $save = $target->targetInsert($data,$option['insert'] ?? null);

                elseif($action === 'update' && is_int($int))
                $save = $target->targetUpdate($data,$int,$option['update'] ?? null);

                elseif($action === 'delete' && is_int($int))
                $save = $target->targetDelete($int,$option['delete'] ?? null);

                else
                static::throw('invalidAction',$one['action']);

                $one['save'] = $save;
            }

            $return['data'][$i] = $one;
            $i++;
        }

        $return['total'] = $this->makeTotal($return['data']);
        $return = $this->prepareReturn($return,$option);

        return $return;
    }
}
?>