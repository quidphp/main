<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
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


    // invoke
    // invoke la méthode
    public function __invoke(...$args)
    {
        return static::throw('notAllowed');
    }


    // toString
    // cast l'objet en string
    public function __toString():string
    {
        return static::class;
    }


    // isset
    // isset sur propriété innacessible
    public function __isset(string $key)
    {
        return static::throw('notAllowed');
    }


    // get
    // get sur propriété innacessible
    public function __get(string $key)
    {
        return static::throw('notAllowed',$key);
    }


    // set
    // set sur propriété innacessible
    public function __set(string $key,$value)
    {
        return static::throw('notAllowed',$key);
    }


    // unset
    // unset sur propriété innacessible
    public function __unset(string $key)
    {
        return static::throw('notAllowed',$key);
    }


    // toArray
    // cast l'objet en array
    public function toArray():array
    {
        return get_object_vars($this);
    }


    // toJson
    // cast l'objet en json
    public function toJson():?string
    {
        return Base\Json::encode($this);
    }


    // cast
    // utiliser pour transformer des objet dans les classes base
    public function _cast()
    {
        return static::throw('notAllowed');
    }


    // serialize
    // ce qui se passe en cas de serialize
    public function serialize()
    {
        return static::throw('notAllowed');
    }


    // unserialize
    // ce qui se passe en cas de unserialize
    public function unserialize($data)
    {
        return static::throw('notAllowed');
    }


    // jsonSerialize
    // ce qui se passe en cas de jsonSerialize
    public function jsonSerialize()
    {
        return static::throw('notAllowed');
    }


    // splId
    // retourne le id unique de l'objet
    public function splId():int
    {
        return Base\Obj::id($this);
    }


    // splHash
    // retourne le hash de l'objet
    public function splHash():string
    {
        return Base\Obj::hash($this);
    }


    // help
    // retourne un tableau d'aide sur l'objet de la classe
    // par défaut private est false
    public function help(bool $private=false,bool $deep=false):array
    {
        $return = [];
        $vars = ($private === true)? get_object_vars($this):null;
        $methods = ($private === true)? get_class_methods($this):null;

        return Base\Obj::info($this,$vars,$methods,$deep);
    }
}
?>