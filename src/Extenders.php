<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main;

// extenders
// class for a collection containing many extender objects
class Extenders extends MapObj
{
    // config
    protected static array $config = [];


    // dynamique
    protected ?array $mapAllow = ['set','unset','remove','filter','sort','serialize','clone']; // méthodes permises
    protected $mapIs = Extender::class; // classe d'objet permis


    // construct
    // construit l'objet extenders, doit fournir null ou un array avec clé string
    final public function __construct(?array $value=null)
    {
        if(!empty($value))
        $this->sets($value);
    }


    // set
    // envoie une exception si key n'est pas string
    // renvoie au set de map
    final public function set($key,$value):self
    {
        if(!is_string($key))
        static::throw('onlyAcceptsStringKeys');

        return parent::set($key,$value);
    }
}
?>