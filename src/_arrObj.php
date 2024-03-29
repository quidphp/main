<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main;
use Quid\Base;

// _arrObj
// trait that provides methods to respect the ArrayAccess, Countable and Iterator native interfaces
trait _arrObj
{
    // dynamique
    protected int $index = 0; // indique la position actuelle du arrobj, lors des foreach


    // arr
    // méthode abstracte à implémenter dans chaque classe utilisant le trait
    // la méthode peut retourner la référence du tableau à utiliser, sinon offset et offunset ne fonctionne pas
    // mettre la méthode protégé pour empêcher les modifications du tableau par l'extérieur
    abstract protected function arr():array;


    // current
    // retourne la valeur du tableau à l'index courante
    final public function current():mixed
    {
        return Base\Arr::index($this->index,$this->arr());
    }


    // key
    // retourne la clé du tableau à l'index courant
    final public function key():mixed
    {
        return Base\Arr::indexKey($this->index,$this->arr());
    }


    // next
    // incrémente l'index du tableau
    final public function next():void
    {
        $this->index++;
    }


    // rewind
    // ramène l'index du tableau à 0
    final public function rewind():void
    {
        $this->index = 0;
    }


    // valid
    // retourne vrai si l'index existe dans le tableau
    final public function valid():bool
    {
        return Base\Arr::indexExists($this->index,$this->arr());
    }


    // count
    // retourne le nombre d'élément dans le tableau
    final public function count():int
    {
        return count($this->arr());
    }


    // offsetExists
    // retourne vrai si la clé existe dans le tableau
    public function offsetExists($key):bool
    {
        return Base\Arr::keyExists($key,$this->arr());
    }


    // offsetGet
    // retourne la valeur de la clé dans le tableau
    // envoie une exception si non existant
    public function offsetGet($key):mixed
    {
        if(!$this->offsetExists($key))
        static::throw('arrayAccess','doesNotExist');

        return Base\Arr::get($key,$this->arr());
    }


    // offsetSet
    // ajoute ou change la valeur d'une clé dans le tableau
    // si clé est null, ajoute une clé numérique à la fin du tableau
    public function offsetSet($key,$value):void
    {
        Base\Arr::setRef($key,$value,$this->arr());
    }


    // offsetUnset
    // enlève une clé dans le tableau
    // envoie une exception si non existant
    public function offsetUnset($key):void
    {
        if(!$this->offsetExists($key))
        static::throw('arrayAccess','doesNotExist');

        Base\Arr::unsetRef($key,$this->arr());
    }
}
?>