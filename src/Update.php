<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main;

// update
// class for a collection containing an update-only array (cannot insert after initialization)
class Update extends Map
{
    // config
    protected static array $config = [];


    // dynamique
    protected ?array $mapAllow = ['set','serialize','clone']; // méthode permises


    // set
    // comme set, mais vérifie que la clé existe
    final public function set($key,$value):self
    {
        if($key === null || !$this->exists($key))
        static::throw('cannotInsertNewKey');

        return parent::set($key,$value);
    }
}
?>