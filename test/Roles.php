<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 * Readme: https://github.com/quidphp/main/blob/master/README.md
 */

namespace Quid\Test\Main;
use Quid\Base;
use Quid\Main;

// roles
// class for testing Quid\Main\Roles
class Roles extends Base\Test
{
    // trigger
    final public static function trigger(array $data):bool
    {
        // prepare
        $roles = new Main\Roles();
        $admin = new Main\Role('admin',80,['admin'=>true]);
        $nobody = new Main\Role('nobody',1,['nobody'=>true]);
        $test = new Main\Role('test',20);
        $rolesIs = new Main\Roles();

        // construct

        // onPrepareKey

        // onPrepareValue

        // isOne
        $rolesIs->add($admin,$nobody,$test);
        assert($rolesIs->isOne('admin'));

        // isAll
        assert(!$rolesIs->isAll('admin'));

        // isNobody
        assert(!$rolesIs->isNobody());
        assert($nobody->roles()->isNobody());

        // isSomebody
        assert($rolesIs->isSomebody());
        assert(!$nobody->roles()->isSomebody());

        // findByName
        assert($rolesIs->findByName('admin')->name() === 'admin');

        // add
        $roles->add($admin,$nobody,$test);
        assert($roles->isCount(3));

        // nobody
        assert($roles->nobody() === $nobody);

        // main
        assert($roles->main() === $admin);

        // makeFromArray
        $roles2 = Main\Roles::makeFromArray(['ok'=>25,'james'=>[21,['shared'=>true]]]);
        assert(current($roles2->keys()) === 25);
        $ok = $roles2->get(25);
        assert($ok->name() === 'ok');
        assert($roles2->first()->permission() === 25);
        assert($roles2->sortDefault()->first()->permission() === 21);

        // map
        assert($roles->get('admin') === $admin);
        assert($roles->get('admiz') === null);
        assert($roles->get(80) === $admin);
        assert($roles->get($admin) === $admin);
        assert(!$roles->in($ok));
        assert($roles->in($admin));
        assert(!$roles->in(2));
        assert(!$roles->exists($ok));
        assert($roles->exists($admin));
        assert($roles->exists(80));
        assert($roles->pair('name')[1] === 'nobody');
        assert(count($roles->group('name')) === 3);
        assert($roles->sortBy('permission',false) !== $roles);
        assert($roles->sortBy('permission',false)->first()->permission() === 80);
        assert($roles->filterReject(1) !== $roles);
        assert($roles->filterReject(1)->isCount(2));
        assert($roles->filterReject($roles)->isEmpty());
        assert($roles->filter(fn($role) => $role->permission() === 80) !== $roles);
        assert($roles->filter(fn($role) => $role->permission() === 80)->isCount(1));
        assert($roles->add($roles2)->isCount(5));

        return true;
    }
}
?>