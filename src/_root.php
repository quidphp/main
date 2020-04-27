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

// _root
// trait that provides basic object methods and magic methods
trait _root
{
    // trait
    use _cache;
    use _overload;
    use _throw;
    use _attr;


    // __invoke
    // invoke la méthode
    public function __invoke(...$args)
    {
        static::throw('notAllowed');

        return;
    }


    // __toString
    // cast l'objet en string
    public function __toString():string
    {
        return static::class;
    }


    // __isset
    // isset sur propriété innacessible
    public function __isset(string $key):bool
    {
        static::throw('notAllowed');

        return false;
    }


    // __get
    // get sur propriété innacessible
    public function __get(string $key)
    {
        static::throw('notAllowed',$key);

        return;
    }


    // __set
    // set sur propriété innacessible
    public function __set(string $key,$value):void
    {
        static::throw('notAllowed',$key);

        return;
    }


    // __unset
    // unset sur propriété innacessible
    public function __unset(string $key):void
    {
        static::throw('notAllowed',$key);

        return;
    }


    // __serialize
    // ce qui se passe en cas de serialize
    public function __serialize():array
    {
        static::throw('notAllowed');

        return [];
    }


    // __unserialize
    // ce qui se passe en cas de unserialize
    public function __unserialize(array $data):void
    {
        static::throw('notAllowed');

        return;
    }


    // jsonSerialize
    // ce qui se passe en cas de jsonSerialize, utiliser par l'interface
    public function jsonSerialize()
    {
        static::throw('notAllowed');

        return;
    }


    // cast
    // utiliser pour transformer des objet dans les classes base
    public function _cast()
    {
        static::throw('notAllowed');

        return;
    }


    // toArray
    // cast l'objet en array
    public function toArray():array
    {
        return get_object_vars($this);
    }


    // toJson
    // cast l'objet en json
    final public function toJson():?string
    {
        return Base\Json::encode($this);
    }


    // serialize
    // retourne une version serialize de l'objet
    final public function serialize():string
    {
        return serialize($this);
    }


    // hasProperty
    // retourne vrai si l'objet a la propriété
    final public function hasProperty(string $prop):bool
    {
        return property_exists($this,$prop);
    }


    // hasMethod
    // retourne vrai si l'objet a la méthode
    final public function hasMethod(string $method):bool
    {
        return method_exists($this,$method);
    }


    // splId
    // retourne le id unique de l'objet
    final public function splId():int
    {
        return Base\Obj::id($this);
    }


    // splHash
    // retourne le hash de l'objet
    final public function splHash():string
    {
        return Base\Obj::hash($this);
    }


    // callThis
    // appele une closure avec un bind de l'objet courant comme this
    final public function callThis(\Closure $closure,...$args)
    {
        return Base\Call::bindTo($this,$closure,...$args);
    }


    // help
    // retourne un tableau d'aide sur l'objet de la classe
    // par défaut private est false
    final public function help(bool $private=false,bool $deep=false):array
    {
        $return = [];
        $vars = ($private === true)? get_object_vars($this):null;
        $methods = ($private === true)? get_class_methods($this):null;

        return Base\Obj::info($this,$vars,$methods,$deep);
    }
}
?>