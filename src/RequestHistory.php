<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main;
use Quid\Base;

// requestHistory
// class for a collection containing an history of requests
class RequestHistory extends Map
{
    // trait
    use Map\_count;
    use Map\_nav;


    // config
    protected static array $config = [
        'extra'=>['redirectable'=>true] // clé extra à utiliser pour générer la string de requête
    ];


    // dynamique
    protected ?array $mapAllow = ['unshift','push','remove','unsetAfterCount','empty','serialize']; // méthodes permises
    protected array $mapAfter = ['unsetAfterCount'=>50]; // maximum de requête conservés
    protected $mapIs = 'string'; // validate pour l'objet


    // onPrepareValue
    // préparation spéciale si la valeur est une instance de request
    // permet de retourner une valeur identique dans l'objet sans considérer le timestamp
    final protected function onPrepareValue($return)
    {
        if($return instanceof Request)
        {
            $extra = $this->extra(true);
            $return = $return->str($extra);

            if(is_string($return) && !empty($return))
            {
                $r = Base\Http::arr($return,$extra);
                unset($r['timestamp']);

                foreach ($this->arr() as $value)
                {
                    $v = Base\Http::arr($value,$extra);

                    if($r === Base\Arr::keyStrip('timestamp',$v))
                    $return = $value;
                }
            }
        }

        return $return;
    }


    // onPrepareReturns
    // prépare le retour pour indexes, gets et slice
    final protected function onPrepareReturns(array $array):self
    {
        return new static($array);
    }


    // cast
    // cast de l'historique, retourne le count
    final public function _cast()
    {
        return $this->count();
    }


    // hasUri
    // retourne vrai si l'uri est dans l'objet
    final public function hasUri($value):bool
    {
        $uris = $this->absolute();
        return is_string($value) && in_array($value,$uris,true);
    }


    // hasCurrentUri
    // retourne vrai si l'uri courante est dans l'objet
    final public function hasCurrentUri():bool
    {
        return $this->hasUri(Base\Request::absolute());
    }


    // add
    // ajoute une requête à l'historique
    final public function add(Request $value):self
    {
        return $this->unshift($this->onPrepareValue($value));
    }


    // addUnique
    // ajoute une requête à l'historique seulement si elle n'existe pas déjà
    final public function addUnique(Request $value):self
    {
        $this->remove($value);
        $this->add($value);

        return $this;
    }


    // previous
    // retourne la dernière uri dans l'historique qui n'est pas la courante
    // si hasExtra est true, la requête doit contenir les clés->valeurs de extra
    final public function previous(bool $hasExtra=true):?array
    {
        $return = null;
        $extra = $this->extra();

        foreach ($this->all() as $key => $value)
        {
            if($value['absolute'] !== Base\Request::absolute())
            {
                if($hasExtra === false || Base\Arr::hasSlices($extra,$value))
                {
                    $return = $value;
                    break;
                }
            }
        }

        return $return;
    }


    // previousRequest
    // retourne la requête précédente
    final public function previousRequest(bool $hasExtra=true):?Request
    {
        $return = null;
        $previous = $this->previous($hasExtra);

        if(!empty($previous))
        $return = Request::newOverload($previous);

        return $return;
    }


    // absolute
    // retourne toutes les uris absoluts de requête dans un tableau
    final public function absolute():array
    {
        $return = [];

        foreach ($this->all() as $key => $value)
        {
            $return[$key] = $value['absolute'];
        }

        return $return;
    }


    // request
    // retourne un tableau avec tous les objets requêtes
    final public function request():array
    {
        $return = [];

        foreach ($this->all() as $key => $value)
        {
            $return[$key] = Request::newOverload($value);
        }

        return $return;
    }


    // all
    // retourne toutes les requête
    final public function all():array
    {
        $return = [];
        $extra = $this->extra(true);

        foreach ($this->arr() as $key => $value)
        {
            $arr = Base\Http::arr($value,$extra);

            if(static::isArrayValid($arr))
            $return[$key] = $arr;
        }

        return $return;
    }


    // extra
    // retourne le tableau extra
    final public function extra(bool $keys=false):array
    {
        $return = $this->getAttr('extra') ?? [];

        if($keys === true && !empty($return))
        $return = array_keys($return);

        return $return;
    }


    // isArrayValid
    // retourne vrai si le tableau est valide pour créer une requête
    final public static function isArrayValid($value):bool
    {
        return is_array($value) && Base\Arr::keysExists(['absolute','method','timestamp'],$value);
    }
}
?>