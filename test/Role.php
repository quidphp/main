<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Test\Main;
use Quid\Base;
use Quid\Main;

// role
// class for testing Quid\Main\Role
class Role extends Base\Test
{
    // trigger
    final public static function trigger(array $data):bool
    {
        // construct
        $admin = new Main\Role('Admin',80,['admin'=>true]);
        $nobody = new Main\Role('nobody',1,['nobody'=>true]);

        // clone
        $adminClone = $admin->clone();
        assert($adminClone !== $admin);
        assert(count($admin->toArray()) === 4);
        assert(!empty($admin->toJson()));
        $serialize = serialize($admin);
        assert($admin->serialize() === $serialize);
        assert(unserialize($serialize)->permission() === 80);

        // cast
        assert($nobody->_cast() === 1);

        // setPermission

        // permission
        assert($admin->permission() === 80);

        // setName

        // name
        assert($admin->name() === 'Admin');

        // is
        assert(!$nobody->is('bla'));

        // isNobody
        assert($nobody->isNobody());
        assert(!$admin->isNobody());

        // isSomebody
        assert(!$nobody->isSomebody());
        assert($admin->isSomebody());

        // output
        assert($admin->output() === ['permission'=>80,'name'=>'Admin']);

        // useAlso
        assert($admin->useAlso() === null);

        // roles
        assert($admin->roles()->isCount(1));

        return true;
    }
}
?>