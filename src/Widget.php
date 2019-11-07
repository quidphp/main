<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main;

// widget
// abstract class that provides basic methods for a widget
abstract class Widget extends Root
{
    // config
    public static $config = [];


    // dynamique
    protected $callback = []; // tableau de callback pour la classe


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
    final public function setCallback(string $key,?callable $value):self
    {
        $this->callback[$key] = $value;

        return $this;
    }


    // callback
    // retourne un callback lié, si existant
    final public function callback(string $key):?callable
    {
        return (array_key_exists($key,$this->callback))? $this->callback[$key]:null;
    }
}
?>