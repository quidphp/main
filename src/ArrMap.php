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

// arrMap
// abstract class that provides base methods to make a collection
abstract class ArrMap extends ArrObj
{
    // trait
    use _serialize;


    // config
    public static $config = [];


    // dynamique
    protected $data = []; // données de la map


    // toString
    // affiche le dump de la map
    public function __toString():string
    {
        return Base\Debug::varGet($this->arr());
    }


    // clone
    // vérifie si clone est allowed
    public function __clone()
    {
        return;
    }


    // offsetExists
    // envoie à la méthode exist lors de l'accès tableau
    public function offsetExists($key):bool
    {
        return $this->exists($key);
    }


    // offsetGet
    // envoie à la méthode get lors de l'accès tableau
    // envoie une exception si non existant
    public function offsetGet($key)
    {
        $return = null;

        if($this->exists($key))
        $return = $this->get($key);

        else
        static::throw('arrayAccess','doesNotExist');

        return $return;
    }


    // offsetSet
    // envoie à la méthode set lors de l'accès tableau
    public function offsetSet($key,$value):void
    {
        $this->set($key,$value);

        return;
    }


    // offsetUnset
    // envoie à la méthode unset lors de l'accès tableau
    // envoie une exception si non existant
    public function offsetUnset($key):void
    {
        if($this->exists($key))
        $this->unset($key);

        else
        static::throw('arrayAccess','doesNotExist');

        return;
    }


    // jsonSerialize
    // serialize l'objet avec json_encode
    // encode seulement data
    public function jsonSerialize()
    {
        return $this->data;
    }


    // toArray
    // retourne le tableau
    // n'est pas une référence
    public function toArray():array
    {
        return $this->arr();
    }


    // cast
    // retourne le tableau pour cast
    public function _cast()
    {
        return $this->toArray();
    }


    // arr
    // retourne une référence du tableau
    // méthode protégé pour empêcher des modifications par l'extérieur
    protected function &arr():array
    {
        return $this->data;
    }


    // isEmpty
    // retourne vrai si la map est vide
    final public function isEmpty():bool
    {
        return Base\Arr::isEmpty($this->arr());
    }


    // isNotEmpty
    // retourne vrai si la map n'est pas vide
    final public function isNotEmpty():bool
    {
        return Base\Arr::isNotEmpty($this->arr());
    }


    // isCount
    // retourne vrai si le count est celui spécifié
    final public function isCount($count):bool
    {
        return Base\Arr::isCount($count,$this->arr());
    }


    // isMinCount
    // retourne vrai si le count est plus grand ou égal que celui spécifié
    final public function isMinCount($count):bool
    {
        return Base\Arr::isMinCount($count,$this->arr());
    }


    // isMaxCount
    // retourne vrai si le count est plus petit ou égal que celui spécifié
    final public function isMaxCount($count):bool
    {
        return Base\Arr::isMaxCount($count,$this->arr());
    }


    // each
    // permet de passer la clé et la valeur dans une closure donné en argument
    // la closure est appelé avec this de l'objet courant
    // si on retourne faux, on brise le loop
    final public function each(callable $callable):self
    {
        $i = 0;
        foreach ($this->arr() as $key => $value)
        {
            $r = Base\Call::withObj($this,$callable,$value,$key,$i);

            if($r === false)
            break;

            $i++;
        }

        return $this;
    }


    // empty
    // vide la map
    public function empty()
    {
        $data =& $this->arr();
        $data = [];

        return $this;
    }
}
?>