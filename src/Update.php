<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main;

// update
// class for a collection containing an update-only array (cannot insert after initialization)
class Update extends Map
{
    // config
    public static $config = [];


    // allow
    protected static $allow = ['set','serialize','clone']; // méthode permises


    // set
    // comme set, mais vérifie que la clé existe
    public function set($key,$value):parent
    {
        if($key === null || !$this->exists($key))
        static::throw('cannotInsertNewKey');

        return parent::set($key,$value);
    }
}
?>