<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 * Readme: https://github.com/quidphp/main/blob/master/README.md
 */

namespace Quid\Main;
use Quid\Base;

// map
// class that provides a set of methods for a collection
class Map extends ArrMap
{
    // config
    protected static array $config = [];


    // dynamique
    protected $mapIs = null; // les valeurs doivent passés ce test de validation ou exception, si is est true renvoie à la méthode dynamique is
    protected ?array $mapAllow = null; // méthodes permises par la classe
    protected ?array $mapDeny = null; // méthodes interdites par la classe
    protected array $mapAfter = []; // les méthodes after, peut y avoir arguments ou non, est public car pourrait être changé dans app


    // construct
    // construit la map, possible de set les données
    // par défaut utilise makeOverwrite, celui-ci fait un overwrite même si la permission n'est pas dans allow
    public function __construct($data=null)
    {
        $this->makeAttr(null);

        if(!empty($data))
        $this->makeOverwrite($data);
    }


    // clone
    // vérifie si clone est allowed
    public function __clone()
    {
        $this->checkAllowed('clone');
        parent::__clone();
    }


    // serialize
    // serialize un objet map
    // toutes les propriétés sont serialize
    public function __serialize():array
    {
        $this->checkAllowed('serialize');

        return parent::__serialize();
    }


    // unserialize
    // unserialize un objet map
    // si une des propritétés n'existe pas, envoie une exception
    public function __unserialize(array $data):void
    {
        $this->checkAllowed('serialize');
        parent::__unserialize($data);
    }


    // jsonSerialize
    // serialize l'objet avec json_encode
    // encode seulement data
    public function jsonSerialize():array
    {
        $this->checkAllowed('jsonSerialize');
        return parent::jsonSerialize();
    }


    // onPrepareThis
    // permet de préparer l'objet avant les méthodes de modification
    // par défaut filter map sont clonés
    protected function onPrepareThis(string $method):self
    {
        return $this;
    }


    // onPrepareKey
    // prépare une clé pour une méthode comme get et slice
    // par défaut, les clés non scalar sont envoyés dans obj/cast et les array dans arrs/keyPrepare
    protected function onPrepareKey($return)
    {
        if(!$return instanceof self)
        {
            if(!is_scalar($return))
            $return = Base\Obj::cast($return);

            if(is_array($return))
            $return = Base\Arrs::keyPrepare($return);
        }

        return $return;
    }


    // onPrepareValue
    // prépare une valeur pour une méthode comme keys et search
    protected function onPrepareValue($return)
    {
        return $return;
    }


    // onPrepareValueSet
    // prépare une valeur pour la méthode set
    protected function onPrepareValueSet($return)
    {
        return $return;
    }


    // onPrepareReplace
    // méthode appelé avec le contenu des méthodes de remplacement
    // utilise la méthode magique quid toArray
    protected function onPrepareReplace($return)
    {
        if(is_object($return) && method_exists($return,'toArray'))
        $return = $return->toArray();

        return $return;
    }


    // onPrepareReturn
    // prépare le retour d'une valeur, pour get
    protected function onPrepareReturn($return)
    {
        return $return;
    }


    // onPrepareReturns
    // prépare le retour de plusieurs valeurs, pour gets et slice
    protected function onPrepareReturns(array $return)
    {
        return $return;
    }


    // onCheckArr
    // callback pour les classes qui étendent pour vérifier arr
    protected function onCheckArr():void
    {
        return;
    }


    // arr
    // retourne une référence du tableau
    // méthode protégé pour empêcher des modifications par l'extérieur
    protected function &arr():array
    {
        $this->onCheckArr();

        return $this->data;
    }


    // clone
    // retourne un close de l'objet
    final public function clone()
    {
        $this->checkAllowed('clone');
        return clone $this;
    }


    // recursive
    // retourne la map et toutes les maps contenus de façon récursives dans un tableau multidimensionnel
    final public function recursive():array
    {
        $return = $this->arr();

        foreach ($return as $key => $value)
        {
            if($value instanceof self)
            $return[$key] = $value->recursive();
        }

        return $return;
    }


    // prepareKeys
    // prepare les clés pour les méthodes qui soumettent plusieurs clés comme exists, gets et unset
    final protected function prepareKeys(...$keys):array
    {
        $return = [];

        foreach ($keys as $key)
        {
            $key = $this->onPrepareKey($key);

            if($key instanceof self)
            $return = Base\Arr::merge($return,...$key->keys());

            else
            $return[] = $key;
        }

        return $return;
    }


    // prepareValues
    // prépare plusieurs valeurs pour les méthodes qui soumette plusieurs valeurs comme in et remove
    final protected function prepareValues(...$values):array
    {
        $return = [];

        foreach ($values as $value)
        {
            $value = $this->onPrepareValue($value);

            if($value instanceof self)
            $return = Base\Arr::merge($return,...$value->values());

            else
            $return[] = $value;
        }

        return $return;
    }


    // prepareReplaces
    // prépare plusieurs valeurs utilisé par une méthode de remplacement
    final protected function prepareReplaces(...$values):array
    {
        $return = [];

        foreach ($values as $value)
        {
            $return[] = $this->onPrepareReplace($value);
        }

        return $return;
    }


    // checkBefore
    // vérifie que les valeurs passent le test is avant l'écriture à l'objet, sinon envoie une exception
    // bool initial permet de spécifier si chaque valeur doit être valider individuellement
    // si static is est true, fait appel à la méthode dynamique is
    final protected function checkBefore(bool $array=false,...$values):void
    {
        $is = $this->mapIs;

        if(!empty($is))
        {
            foreach ($values as $value)
            {
                $exception = false;
                $is = ($is === true)? [$this,'is']:$is;
                $call = ($is === true)? 'is':$is;

                if($array === true && is_array($value))
                {
                    if(!Base\Arr::validate($is,$value))
                    $exception = true;
                }

                elseif(!Base\Validate::is($is,$value))
                $exception = true;

                if($exception === true)
                static::throw('onlyAccepts',$call);
            }
        }
    }


    // checkAfter
    // permet de lancer un ou plusieurs callbacks après une modification au tableau
    // utiliser par set, sets, unset, empty, overwrite etr emove
    protected function checkAfter():self
    {
        $after = $this->mapAfter;

        foreach ($after as $key => $value)
        {
            $method = null;
            $arg = [];

            if(is_string($key))
            {
                $method = $key;
                $arg = (array) $value;
            }

            elseif(is_string($value))
            $method = $value;

            if(!empty($method))
            $this->$method(...$arg);
        }

        return $this;
    }


    // checkAllowed
    // retourne vrai si la ou les méthodes sont permis, sinon lance une exception
    protected function checkAllowed(string ...$values)
    {
        foreach ($values as $value)
        {
            if(!$this->isAllowed($value))
            static::throw($value);
        }

        return $this;
    }


    // is
    // méthode appelé si static is est true
    public function is($value):bool
    {
        return false;
    }


    // isValidate
    // retourne vrai si toutes les valeurs réponde à la condition validate
    final public function isValidate($validate):bool
    {
        return Base\Arr::validate($validate,$this->arr());
    }


    // checkMinCount
    // envoie une exception si le min count n'est pas respecté
    final public function checkMinCount($count):self
    {
        if(!$this->isMinCount($count))
        static::throw($count);

        return $this;
    }


    // checkMaxCount
    // envoie une exception si le max count n'est pas respecté
    final public function checkMaxCount($count):self
    {
        if(!$this->isMaxCount($count))
        static::throw($count);

        return $this;
    }


    // exists
    // retourne vrai si la ou les clés existe dans la map
    public function exists(...$keys):bool
    {
        return Base\Arr::keysExists($this->prepareKeys(...$keys),$this->arr(),$this->isSensitive());
    }


    // existsFirst
    // retourne la première clé dans le tableau
    final public function existsFirst(...$keys)
    {
        $return = null;

        foreach ($keys as $key)
        {
            if($this->exists($key))
            {
                $return = $key;
                break;
            }
        }

        return $return;
    }


    // checkGet
    // vérifie que la clé existe et retourne la valeur
    // sinon envoie une exception
    final public function checkGet($key)
    {
        $return = null;

        if($this->exists($key))
        $return = $this->get($key);

        else
        static::throw($key);

        return $return;
    }


    // checkExists
    // vérifie que toutes les clés existent sinon envoie une exception
    final public function checkExists(...$keys):self
    {
        if(!$this->exists(...$keys))
        static::throw(...$keys);

        return $this;
    }


    // in
    // retourne vrai si la ou les valeurs sont dans la map
    public function in(...$values):bool
    {
        return Base\Arr::ins($this->prepareValues(...$values),$this->arr(),$this->isSensitive());
    }


    // inFirst
    // retourne la première valeur dans le tableau
    // n'a pas à être étendu
    final public function inFirst(...$values)
    {
        $return = null;

        foreach ($values as $value)
        {
            if($this->in($value))
            {
                $return = $value;
                break;
            }
        }

        return $return;
    }


    // checkIn
    // vérifie que toutes les valeurs sont dans l'objet sinon envoie une exception
    final public function checkIn(...$values):self
    {
        if(!$this->in(...$values))
        static::throw();

        return $this;
    }


    // keys
    // retourne un tableau des clés dans la map
    // possibilité de retourner les clés ayant la valeur
    public function keys($value=null):array
    {
        return Base\Arr::keys($this->arr(),$this->onPrepareValue($value),$this->isSensitive());
    }


    // search
    // retourne la première clé d'une valeur dans la map
    public function search($value)
    {
        return Base\Arr::search($this->onPrepareValue($value),$this->arr(),$this->isSensitive());
    }


    // values
    // retourne un tableau des valeurs dans la map
    // n'a pas à être étendu
    public function values($is=null):array
    {
        return Base\Arr::values($this->arr(),$is);
    }


    // first
    // retourne la première valeur du tableau
    // n'a pas à être étendu
    final public function first()
    {
        $return = Base\Arr::valueFirst($this->arr());
        return $this->onPrepareReturn($return);
    }


    // last
    // retourne la dernière valeur du tableau
    // n'a pas à être étendu
    final public function last()
    {
        $return = Base\Arr::valueLast($this->arr());
        return $this->onPrepareReturn($return);
    }


    // find
    // retourne la première valeur du tableau répondant true à la condition
    final public function find(\Closure $closure)
    {
        return $this->onPrepareReturn(parent::find($closure));
    }


    // get
    // retourne une valeur d'une clé dans la map
    public function get($key)
    {
        return $this->onPrepareReturn(Base\Arr::get($this->onPrepareKey($key),$this->arr(),$this->isSensitive()));
    }


    // gets
    // retourne plusieurs valeurs de clés dans la map
    public function gets(...$keys)
    {
        return $this->onPrepareReturns(Base\Arr::gets($this->prepareKeys(...$keys),$this->arr(),$this->isSensitive()));
    }


    // index
    // retourne une valeur d'un index dans la map
    public function index($index)
    {
        return (is_int($index))? $this->onPrepareReturn(Base\Arr::index($index,$this->arr())):null;
    }


    // indexes
    // retourne plusieurs indexes de clés dans la map
    public function indexes(...$indexes)
    {
        return $this->onPrepareReturns(Base\Arr::indexes($indexes,$this->arr()));
    }


    // slice
    // permet de slice une ou plusieurs clés->valeurs de la map
    // utilise les clés start et end
    final public function slice($start,$end)
    {
        return $this->onPrepareReturns(Base\Arr::slice($this->onPrepareKey($start),$this->onPrepareKey($end),$this->arr(),$this->isSensitive()));
    }


    // sliceIndex
    // permet de slice une ou plusieurs clés->valeurs de la map
    // utilise les clés offset et length
    final public function sliceIndex(int $offset,?int $length)
    {
        return $this->onPrepareReturns(Base\Arr::sliceIndex($offset,$length,$this->arr()));
    }


    // set
    // ajoute ou change une clé valeur dans la map, accepte une clé null
    // une exception est envoyé si la clé est de format invalide
    // la clé est envoyé dans onPrepareKey
    public function set($key,$value):self
    {
        $this->checkAllowed('set')->checkBefore(false,$value);
        $return = $this->onPrepareThis('set');
        $key = $this->onPrepareKey($key);
        $value = $this->onPrepareValueSet($value);

        if($key === null || Base\Arr::isKey($key))
        Base\Arr::setRef($key,$value,$return->arr(),$this->isSensitive());

        else
        static::throw('invalidKey');

        return $return->checkAfter();
    }


    // sets
    // ajoute un change plusieurs valeurs dans la map
    // n'a pas à être étendu
    public function sets(array $values):self
    {
        foreach ($values as $key => $value)
        {
            $this->set($key,$value);
        }

        return $this;
    }


    // unset
    // enlève une ou plusieurs clés dans la map
    public function unset(...$keys):self
    {
        $this->checkAllowed('unset');
        $return = $this->onPrepareThis('unset');
        Base\Arr::unsetsRef($this->prepareKeys(...$keys),$return->arr(),$this->isSensitive());

        return $return->checkAfter();
    }


    // remove
    // enlève une ou plusieurs valeurs dans la map
    public function remove(...$values):self
    {
        $this->checkAllowed('remove');
        $return = $this->onPrepareThis('remove');
        $data =& $return->arr();
        $data = Base\Arr::valuesStrip($this->prepareValues(...$values),$data,$this->isSensitive());

        return $return->checkAfter();
    }


    // push
    // pousse une valeur tel quel dans la map
    public function push(...$values):self
    {
        $this->checkAllowed('push')->checkBefore(false,...$values);
        $return = $this->onPrepareThis('push');
        $data =& $return->arr();
        $data = Base\Arr::push($data,...$values);

        return $return->checkAfter();
    }


    // unshift
    // ajoute une valeur tel quel au début de la map
    public function unshift(...$values):self
    {
        $this->checkAllowed('unshift')->checkBefore(false,...$values);
        $return = $this->onPrepareThis('unshift');
        $data =& $return->arr();
        $data = Base\Arr::unshift($data,...$values);

        return $return->checkAfter();
    }


    // overwrite
    // remplace le contenu de la map par un tableau
    // possible de remplacer par une autre instance de map
    final public function overwrite($value):self
    {
        $this->checkAllowed('overwrite');
        $this->makeOverwrite($value);

        return $this;
    }


    // makeOverwrite
    // permet de faire un overwrite sur un objet sans avoir la permission dans allow
    protected function makeOverwrite($value):void
    {
        $return = $this->onPrepareThis('overwrite');
        $data =& $return->arr();
        $value = $return->onPrepareReplace($value);

        if(is_array($value))
        {
            $this->checkBefore(false,...array_values($value));
            $data = $value;
        }

        else
        static::throw('requireArray');

        $return->checkAfter();
    }


    // empty
    // vide la map
    public function empty()
    {
        $this->checkAllowed('empty');
        $return = $this->onPrepareThis('empty');
        $data =& $return->arr();
        $data = [];

        return $this->checkAfter();
    }


    // isAllowed
    // retourne vrai si la méthode est permis par la classe
    final public function isAllowed($value):bool
    {
        $return = false;
        $allow = $this->mapAllow;
        $deny = $this->mapDeny;

        if($allow === null && $deny === null)
        $return = true;

        elseif(is_string($value))
        {
            if(empty($allow) || (is_array($allow) && in_array($value,$allow,true)))
            $return = true;

            if(!empty($deny) && in_array($value,$deny,true))
            $return = false;
        }

        return $return;
    }


    // isSensitive
    // retourne vrai pour cette classe
    public function isSensitive():bool
    {
        return true;
    }
}
?>