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

// autoload
// class for testing Quid\Main\Autoload
class Autoload extends Base\Test
{
    // trigger
    public static function trigger(array $data):bool
    {
        // construct
        $a = new Main\Autoload('alias');

        // setAttr

        // attr
        assert(count($a->attr()) === 4);

        // initClass

        // storeHit

        // storeMiss

        // getCallable
        assert(is_callable($a->getCallable()));

        // register
        assert($a->register() === true);

        // unregister
        assert($a->unregister() === true);
        assert($a->unregister() === false);

        // findPsr4

        // getPsr4File

        // findAlias

        // findClosure

        // registerPsr4

        // getAlias

        // setAlias

        // setsAlias

        // unsetAlias

        // allAlias
        assert(is_array(Main\Autoload::allAlias()));

        // aliasEnding
        assert(Main\Autoload::aliasEnding() === 'Alias');

        // registerAlias

        // getClosure

        // setClosure
        assert(Main\Autoload::setClosure('Test\Ok\What','James',function() {}) === null);
        assert(Main\Autoload::setClosure('Test\Ok\What\Lol','_noway',function() {}) === null);

        // getClosureByNamespace
        assert(Main\Autoload::getClosureByNamespace('test') === []);
        assert(Main\Autoload::getClosureByNamespace('Test\Ok\What') === ['Test\Ok\What\James']);
        assert(Main\Autoload::getClosureByNamespace('Test\Ok') === []);
        assert(Main\Autoload::getClosureByNamespace('Test\Ok',true,true) === ['Test\Ok\What\James']);
        assert(Main\Autoload::getClosureByNamespace('Test\Ok\What',false,false) === ['Test\Ok\What\James']);
        assert(count(Main\Autoload::getClosureByNamespace('Test\Ok\What',false,true)) === 2);
        assert(Main\Autoload::getClosureByNamespace('xyz') === []);

        // allClosure
        assert(Base\Arrs::is(Main\Autoload::allClosure()));

        // registerClosure

        // getOverload
        assert(Main\Autoload::getOverload('james',Classe::class) === Classe::class);
        assert(Main\Autoload::getOverload('james') === null);

        // setOverload

        // setsOverload

        // unsetOverload

        // allOverload
        assert(is_array(Main\Autoload::allOverload()));

        // findOneOrMany
        assert(!empty(Main\Autoload::findOneOrMany([__NAMESPACE__],false,true,true)));
        assert(Main\Autoload::findOneOrMany(\Datetime::class,true,true,true) === [\Datetime::class]);

        // findOne
        assert(Main\Autoload::findOne(\Datetime::class,true,true) === true);
        assert(Main\Autoload::findOne(\Datetime::class,true,false) === null);

        // findMany
        assert(!empty(Main\Autoload::findMany(__NAMESPACE__,false,true,true)));

        // requireFile

        // exists

        // phpExtension
        assert(Main\Autoload::phpExtension() === 'php');

        return true;
    }
}
?>