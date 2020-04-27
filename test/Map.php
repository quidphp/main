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

// map
// class for testing Quid\Main\Map
class Map extends Base\Test
{
    // trigger
    final public static function trigger(array $data):bool
    {
        // prepare
        $array = ['test'=>'ok','what'=>'LOL','james'=>2,3];

        // construct
        $map = new Main\Map($array);
        $onlyNumeric = new Main\Map([1,2,3]);
        $empty = new Main\Map();
        $recursive = new Main\Map([$map,$empty,$onlyNumeric]);

        // arrMap
        assert($map->toArray() === ['test'=>'ok','what'=>'LOL','james'=>2,3]);
        $map[0] = 3;
        assert(strlen($map->toJson()) === 42);
        assert($map->_cast() === $map->toArray());
        assert(isset($map['test']));
        assert(!isset($map['testz']));
        assert($map['test'] === 'ok');
        $map['meh'] = 'ok';
        assert($map['meh'] === 'ok');
        unset($map['meh']);
        unset($map[0]);
        $serialize = serialize($map);
        assert(strlen($serialize) === 218);
        $map2 = unserialize($serialize);
        assert($map2 !== $map);
        assert(strlen(json_encode($map)) === 36);
        $mapclone = $map->clone();
        assert($mapclone->toArray() === $map->toArray());
        assert($mapclone !== $map);
        assert(!$map->isEmpty());
        assert($empty->isEmpty());
        assert($map->isNotEmpty());
        assert($map->isCount(3));
        assert($map->isCount([1,2,3]));
        assert($map->isMinCount(3));
        assert($map->isMinCount([1,2,3]));
        assert($map->isMaxCount(3));
        assert($map->isMaxCount([1,2,3]));
        assert($map->each(fn($value,$key) => ($key === 'WHAT')) === false);
        assert($map->each(fn($value,$key) => (is_string($key))) === true);
        assert($map->reduce(null,fn($r,$value) => $r .= $value) === 'okLOL2');
        assert($map->accumulate(null,fn($value) => $value) === 'okLOL2');
        assert(!$map->some(fn($value) => is_array($value)));
        assert($map->some(fn($value) => is_int($value)));
        assert($map->every(fn($value) => is_scalar($value)));
        assert(!$map->every(fn($value) => is_string($value)));
        assert($map->findKey(fn($value) => is_int($value)) === 'james');

        // clone

        // serialize

        // unserialize

        // jsonSerialize

        // onPrepareThis

        // onPrepareKey

        // onPrepareValue

        // onPrepareValueSet

        // onPrepareReplace

        // onPrepareReturn

        // onPrepareReturns

        // onCheckArr

        // arr

        // recursive
        assert(Base\Arr::isMulti($recursive->recursive(true)));

        // prepareKeys

        // prepareValues

        // prepareReplaces

        // checkBefore

        // checkAfter

        // checkAllowed

        // is

        // isValidate
        assert(!$map->isValidate('string'));

        // checkMinCount
        assert($map->checkMinCount(2) === $map);

        // checkMaxCount
        assert($map->checkMaxCount(4) === $map);

        // exists
        assert($map->exists('test'));
        assert(!$map->exists('TEST'));
        assert($map->exists('test','what'));
        assert(!$map->exists('test',3));
        assert(!$map->exists(3));

        // existsFirst
        assert($map->existsFirst(2,'as','what',0) === 'what');

        // checkGet
        assert($map->checkGet('test') === 'ok');

        // checkExists
        assert($map->checkExists('test','what') === $map);

        // in
        assert($map->in('LOL'));
        assert($map->in('ok','LOL'));
        assert(!$map->in('ok','LOLz'));
        assert(!$map->in('LOLz'));

        // inFirst
        assert($map->inFirst('as','LOL',0) === 'LOL');

        // checkIn
        assert($map->checkIn('ok','LOL') === $map);

        // keys
        assert($map->keys() === ['test','what','james']);

        // search
        assert($map->search(2) === 'james');

        // values
        assert($map->values() === ['ok','LOL',2]);
        assert($map->values('int') === [2]);

        // first
        assert($map->first() === 'ok');

        // last
        assert($map->last() === 2);

        // find
        assert($map->find(fn($value,$key) => is_int($value)) === 2);
        assert($map->find(fn($value) => is_string($value)) === 'ok');

        // get
        assert($map->get('test') === 'ok');
        assert($map->get('addsasda') === null);

        // gets
        assert($map->gets('test') === ['test'=>'ok']);
        assert($map->gets('what','test') === ['what'=>'LOL','test'=>'ok']);

        // index
        assert($map->index(0) === 'ok');

        // indexes
        assert($map->indexes(0,1) === ['ok','LOL']);

        // slice
        assert(count($map->slice('test','james')) === 3);

        // sliceIndex
        assert($map->sliceIndex(0,1) === ['test'=>'ok']);

        // set
        assert($map->set('test','ok2') instanceof Main\Map);
        assert($map->get('test') === 'ok2');
        assert($map->sets(['test'=>'ok22','non'=>false])->count() === 4);

        // sets
        assert($map->sets(['okz'=>'WAAAA'])['okz'] === 'WAAAA');
        assert($map->unset('okz'));

        // unset
        $map->unset('non');
        assert($map->unset('test')->count() === 2);
        assert($map->unset('what','james')->count() === 0);

        // remove
        $map->sets([1,2,2]);
        assert($map->remove(2)->count() === 1);

        // push
        assert($map->push(3) === $map);

        // unshift
        assert($map->unshift(1)->count() === 3);

        // overwrite
        assert($map->overwrite(['test'=>2])->count() === 1);

        // makeOverwrite

        // empty
        assert($map->empty()->count() === 0);
        assert($map->overwrite(['test'=>2])->count() === 1);

        // isAllowed
        assert($map->isAllowed('empty'));

        // isSensitive
        assert($map->isSensitive());

        // ArrObj
        $i = 0;
        $map['ok'] = 2;
        foreach ($map as $key => $value)
        {
            $i++;
        }
        assert($i === 2);
        assert(count($map) === 2);

        return true;
    }
}
?>