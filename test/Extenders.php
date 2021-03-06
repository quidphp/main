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

// extenders
// class for testing Quid\Main\Extenders
class Extenders extends Base\Test
{
    // trigger
    final public static function trigger(array $data):bool
    {
        // prepare
        $ex = new Main\Extender(__NAMESPACE__);
        $ex2 = new Main\Extender("Quid\Base");

        // construct
        $s = new Main\Extenders(['ex'=>$ex]);
        assert($s->isNotEmpty());

        // set
        $s->set('ex2',$ex2);
        assert($s->count() === 2);

        return true;
    }
}
?>