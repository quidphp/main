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

// catchableException
// class for testing Quid\Main\CatchableException
class CatchableException extends Base\Test
{
    // trigger
    final public static function trigger(array $data):bool
    {
        // construct
        $e = new Main\CatchableException('well');

        // exception
        assert($e instanceof Main\Contract\Catchable);
        assert($e->getCode() === 32);

        return true;
    }
}
?>