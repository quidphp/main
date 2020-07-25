<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main;

// _serialize
// trait that provides methods for serializing and unserializing object
trait _serialize
{
    // __serialize
    // méthode magique pour serializer un objet
    // toutes les propriétés sont serialize
    public function __serialize():array
    {
        return get_object_vars($this);
    }


    // __unserialize
    // méthode magiqu pour unserialize un objet
    // si une des propritétés n'existe pas, envoie une exception
    public function __unserialize(array $data):void
    {
        foreach ($data as $key => $value)
        {
            if(!is_string($key) || !$this->hasProperty($key))
            static::throw('propertyDoesNotExist',$key);

            $this->$key = $value;
        }
    }


    // jsonSerialize
    // converti l'objet en json, utilise toArray
    public function jsonSerialize():array
    {
        return $this->toArray();
    }
}
?>