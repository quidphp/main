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

// update
// class for testing Quid\Main\Update
class Update extends Base\Test
{
    // trigger
    final public static function trigger(array $data):bool
    {
        // construct
        $array = [1=>'test','bla'=>'OK'];
        $u = new Main\Update($array);

        // map
        assert($u->set(1,'bla')->get(1) === 'bla');

        return true;
    }
}
?>