<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Test\Main;
use Quid\Base;
use Quid\Main;

// extender
// class for testing Quid\Main\Extender
class Extender extends Base\Test
{
    // trigger
    public static function trigger(array $data):bool
    {
        // prepare

        // construct
        $ex = new Main\Extender(__NAMESPACE__);

        // onPrepareThis

        // onAddNamespace

        // isExtended
        assert(!$ex->isExtended('test'));

        // areSubClassOf
        assert($ex->areSubClassOf(Base\Test::class));

        // add

        // addNamespace

        // checkNoSubDir

        // notSubClassOf
        assert($ex->notSubClassOf(Base\Test::class) === null);
        assert(is_string($ex->notSubClassOf(Base\Arr::class)));

        // set

        // extended
        assert($ex->extended()->isEmpty());
        assert($ex->isNotEmpty());

        // extendSync

        // overload

        // overloadSync

        // alias

        // getKey
        assert(Main\Extender::getKey('TestClass') === 'TestClass');

        // map
        assert($ex->get('Extender') === __CLASS__);

        return true;
    }
}
?>