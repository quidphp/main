<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Test\Main;
use Quid\Main;
use Quid\Base;

// update
// class for testing Quid\Main\Update
class Update extends Base\Test
{
    // trigger
    public static function trigger(array $data):bool
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