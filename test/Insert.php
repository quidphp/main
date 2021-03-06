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

// insert
// class for testing Quid\Main\Insert
class Insert extends Base\Test
{
    // trigger
    final public static function trigger(array $data):bool
    {
        // construct
        $i = new Main\Insert(['ok'=>2]);

        // map
        assert($i->set('ok2',3)->isCount(2));
        assert($i->set(null,'what')->isCount(3));
        assert($i->push('lol','lol2')->unshift('lolz','lolz2')->isCount(7));

        return true;
    }
}
?>