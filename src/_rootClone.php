<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main;

// _rootClone
// trait that provides a default __clone magic function
trait _rootClone
{
    // trait
    use _root;


    // clone
    // ce qui se passe en cas de clone
    public function __clone()
    {
        return static::throw('notAllowed');
    }
    
    
    // clone
    // retourne un close de l'objet
    public function clone()
    {
        return clone $this;
    }
}
?>