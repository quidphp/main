<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main;

// insert
// class for a collection containing an insert-only array (cannot update)
class Insert extends Map
{
    // config
    protected static array $config = [];


    // dynamique
    protected ?array $mapAllow = ['set','push','unshift','serialize','clone']; // méthode permises


    // set
    // comme set, mais vérifie que la clé n'existe pas
    final public function set($key,$value):self
    {
        if($key !== null && $this->exists($key))
        static::throw('cannotUpdateExistingKey');

        return parent::set($key,$value);
    }
}
?>