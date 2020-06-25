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
    protected static array $config = [];


    // dynamique
    protected array $data = []; // données de la map


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
    }


    // jsonSerialize
    // serialize l'objet avec json_encode
    // encode seulement data
    public function jsonSerialize():array
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
    // permet de faire un each dans l'array
    // si un loop retourne false, brise le loop et retourne false
    // pour être utile, il faut passer une valeur par référence dans la closure (pas de arrow func)
    final public function each(\Closure $closure):bool
    {
        return Base\Arr::each($this->arr(),$closure);
    }


    // some
    // vérifie qu'au moins une entrée du tableau passe le test de la closure
    final public function some(\Closure $closure):bool
    {
        return Base\Arr::some($this->arr(),$closure);
    }


    // every
    // vérifie que toutes les entrée du tableau passe le test de la closure
    final public function every(\Closure $closure):bool
    {
        return Base\Arr::every($this->arr(),$closure);
    }


    // find
    // retourne la première valeur du tableau répondant true à la condition
    public function find(\Closure $closure)
    {
        return Base\Arr::find($this->arr(),$closure);
    }


    // findKey
    // retourne la première clé du tableau dont la valeur répond true à la condition
    final public function findKey(\Closure $closure)
    {
        return Base\Arr::findKey($this->arr(),$closure);
    }


    // reduce
    // permet d'envoyer le contenu du tableau dans reduce
    // l'ordre des arguments est différent que la fonction de base
    // de même la clé est passé en troisième argument
    final public function reduce($return,\Closure $closure)
    {
        return Base\Arr::reduce($return,$this->arr(),$closure);
    }


    // accumulate
    // comme reduce, mais le return est automatiquement append
    // autre différence, si le callback retourne faux brise le loop
    final public function accumulate($return,\Closure $closure)
    {
        return Base\Arr::accumulate($return,$this->arr(),$closure);
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