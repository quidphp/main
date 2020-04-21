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

// mapObj
// class for testing Quid\Main\MapObj
class MapObj extends Base\Test
{
    // trigger
    final public static function trigger(array $data):bool
    {
        // construct
        $x = new Main\MapObj(\Datetime::class);

        // map
        $x->set(null,new \Datetime('now'));
        assert($x->count() === 1);

        return true;
    }
}
?>