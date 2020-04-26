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

// widget
// abstract class that provides basic methods for a widget
abstract class Widget extends Root
{
    // config
    public static array $config = [];


    // dynamique
    protected array $callback = []; // tableau de callback pour la classe


    // toString
    // renvoie le html de l'élément
    final public function __toString():string
    {
        return $this->html();
    }


    // clone
    // clone est permis
    final public function __clone()
    {
        return;
    }


    // toArray
    // retourne la structure de l'élément sous forme de tableau multidimensionnel
    final public function toArray():array
    {
        return $this->structure();
    }


    // output
    // génère le output de l'élément
    abstract public function output():string;


    // structure
    // retourne la structure de l'élément
    abstract public function structure():array;


    // setCallback
    // attribute un tableau ou objet callback à la classe
    final public function setCallback(string $key,?\Closure $closure):self
    {
        $this->callback[$key] = $closure;

        return $this;
    }


    // callback
    // retourne un callback lié, si existant
    final public function callback(string $key):?\Closure
    {
        return (array_key_exists($key,$this->callback))? $this->callback[$key]:null;
    }
}
?>