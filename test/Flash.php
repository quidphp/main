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

// flash
// class for testing Quid\Main\Flash
class Flash extends Base\Test
{
    // trigger
    final public static function trigger(array $data):bool
    {
        // construct
        $f = new Main\Flash();

        // map
        $f['test'] = 2;
        assert($f->get('test') === 2);
        assert($f->get('test') === null);
        $f['test'] = 2;
        $f['bla'] = 3;
        assert($f->gets('bla','test') === ['bla'=>3,'test'=>2]);
        $f['test'] = 2;
        $f['bla'] = 3;
        assert(isset($f['test']));
        assert($f['test'] === 2);
        assert(!isset($f['test']));
        assert($f['bla'] === 3);
        assert($f->isEmpty());
        $f['test'] = 2;
        assert($f->keys() === ['test']);
        $f['test3'] = 2;
        $f['test4'] = 2;
        assert($f instanceof Main\Flash);

        return true;
    }
}
?>