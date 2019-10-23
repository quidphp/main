<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main;

// extenders
// class for a collection containing many extender objects
class Extenders extends Map
{
    // trait
    use Map\_obj;


    // config
    public static $config = [];


    // map
    protected static $allow = ['set','unset','remove','filter','sort','serialize','clone']; // méthodes permises
    protected static $is = true; // renvoie à la méthode is


    // construct
    // construit l'objet extenders, doit fournir null ou un array avec clé string
    public function __construct(?array $value=null)
    {
        if(!empty($value))
        $this->sets($value);

        return;
    }


    // is
    // vérifie que la valeur est une instance de extender
    public function is($value):bool
    {
        return ($value instanceof Extender)? true:false;
    }


    // set
    // envoie une exception si key n'est pas string
    // renvoie au set de map
    public function set($key,$value):parent
    {
        if(!is_string($key))
        static::throw('onlyAcceptsStringKeys');

        return parent::set($key,$value);
    }
}
?>