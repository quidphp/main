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

// insert
// class for a collection containing an insert-only array (cannot update)
class Insert extends Map
{
    // config
    public static $config = [];


    // allow
    protected static $allow = ['set','push','unshift','serialize','clone']; // méthode permises


    // set
    // comme set, mais vérifie que la clé n'existe pas
    final public function set($key,$value):parent
    {
        if($key !== null && $this->exists($key))
        static::throw('cannotUpdateExistingKey');

        return parent::set($key,$value);
    }
}
?>