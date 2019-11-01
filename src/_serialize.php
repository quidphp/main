<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main;

// _serialize
// trait that provides methods for serializing and unserializing object
trait _serialize
{
    // serialize
    // serialize un objet
    // toutes les propriétés sont serialize
    public function serialize():string
    {
        $return = '';
        $data = get_object_vars($this);

        if(is_array($data))
        $return = serialize($data);

        return $return;
    }


    // unserialize
    // unserialize un objet
    // si une des propritétés n'existe pas, envoie une exception
    public function unserialize($data)
    {
        $data = unserialize($data);

        if(is_array($data))
        {
            foreach ($data as $key => $value)
            {
                if(is_string($key) && property_exists($this,$key))
                $this->$key = $value;

                else
                static::throw('propertyDoesNotExist',$key);
            }
        }

        return $this;
    }
    
    
    // jsonSerialize
    // converti l'objet en json, utilise toArray
    public function jsonSerialize():array
    {
        return $this->toArray();
    }
}
?>