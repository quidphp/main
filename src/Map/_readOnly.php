<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main\Map;
use Quid\Main;

// _readOnly
// trait that provides a simple readOnly lock to a collection
trait _readOnly
{
    // dynamique
    protected $readOnly = false; // si l'objet est présentement en mode read only


    // clone
    // ramène readOnly à false sur clone
    final public function __clone()
    {
        $this->readOnly(false);

        return;
    }


    // isReadOnly
    // retourne vrai si l'objet est en mode readOnly
    final public function isReadOnly():bool
    {
        return $this->readOnly;
    }


    // readOnly
    // active ou désactive le mode readOnly
    final public function readOnly(bool $readOnly=true):Main\Map
    {
        $this->readOnly = $readOnly;

        return $this;
    }


    // checkReadOnly
    // retourne l'objet si l'objet n'est pas readOnly, sinon lance une exception
    final protected function checkReadOnly():Main\Map
    {
        if($this->isReadOnly())
        static::throw();

        return $this;
    }


    // checkAllowed
    // retourne l'objet si la méthode est permis, sinon lance une exception
    // exception non envoyé si c'est pour jsonSerialize, serialize ou clone
    final protected function checkAllowed(string ...$values):Main\Map
    {
        foreach ($values as $value)
        {
            if(!in_array($value,static::allowedReadOnlyMethods(),true))
            $this->checkReadOnly();

            if(!$this->isAllowed($value))
            static::throw($value);
        }

        return $this;
    }


    // allowedReadOnlyMethods
    // retourne les méthodes permises même si readOnly est true
    final public static function allowedReadOnlyMethods():array
    {
        return ['filter','jsonSerialize','serialize','clone'];
    }
}
?>