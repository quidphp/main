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

// std
// class for testing Quid\Main\Std
class Std extends Base\Test
{
    // trigger
    final public static function trigger(array $data):bool
    {
        // construct
        $arr = new Main\Std();

        // map
        assert($arr->push(['okv'=>1],1) === $arr);
        assert($arr->are(0,1));
        assert($arr->unshift(['okv'=>1],1) === $arr);
        assert($arr->append(['okz'=>1],1) === $arr);
        assert($arr->prepend(['okz'=>3],9) === $arr);
        assert($arr->isCount(7));
        assert($arr->pop(2) === [1,1]);
        assert($arr->shift() === 1);
        assert($arr->isCount(4));
        assert($arr->replace([3=>['r'=>true]]) === $arr);
        assert($arr[3]['r'] === true);
        assert($arr->splice(0,1)->isCount(2));
        assert($arr->spliceIndex(0,1)->isCount(1));
        assert($arr->insert(0,['ok'=>2]) === $arr);
        assert($arr->insertIndex(0,['ok'=>3]) === $arr);
        assert($arr->isCount(2));
        assert($arr['ok'] === 3);
        $new = $arr->filter(function($value,$key) {
            return (is_int($value));
        });
        assert($new->count() === 1);
        assert($new->map(function($value) {
            return (is_int($value))? true:$value;
        })['ok'] === true);
        $base = new Main\Std([1,'james','ok'=>'test']);
        $base2 = new Main\Std([1,2,3]);
        assert(!$base->isIndexed());
        assert($base2->isIndexed());
        assert(!$base->isSequential());
        assert($base2->isSequential());
        assert($base->isAssoc());
        assert(!$base2->isAssoc());
        assert($base->hasNumericKey());
        assert($base->hasNonNumericKey());
        assert(!$base->hasKeyCaseConflict());
        assert($base->isUni());
        assert(!$base->isMulti());
        assert(!$base->onlyNumeric());
        assert($base2->onlyNumeric());
        assert(!$base->isSet());
        assert($base2->isSet());
        $count = new Main\Std([1,2,3,4,5]);
        assert($count->unsetAfterCount(4)->count() === 4);
        $sort = new Main\Std(['test'=>'ok','james'=>'deux']);
        assert($sort->sort(true)->first() === 'deux');
        assert($sort->sort(false)->first() === 'ok');
        $sequential = new Main\Std(['test'=>'ok','james'=>'deux']);
        assert(!$sequential->isSequential());
        assert($sequential->sequential()->isSequential());

        return true;
    }
}
?>