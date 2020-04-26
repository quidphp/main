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

// calendar
// class for testing Quid\Main\Calendar
class Calendar extends Base\Test
{
    // trigger
    final public static function trigger(array $data):bool
    {
        // construct
        $cal = new Main\Calendar([2018,12]);
        $cal->setCallback('day',fn(int $v) => "-$v-")
        ->setCallback('prev',fn() => 'PREV')
        ->setCallback('next',fn() => 'NEXT');
        $cal2 = clone $cal;
        assert(Base\Arrs::is($cal->toArray()));
        assert($cal->callback('prev') instanceof \Closure);

        // cast
        assert($cal->_cast() === $cal->timestamp());

        // setTimestamp

        // timestamp
        assert(!empty($cal->timestamp()));

        // prevTimestamp
        assert(!empty($cal->prevTimestamp()));
        assert($cal->prevTimestamp() < $cal->timestamp());

        // nextTimestamp
        assert(!empty($cal->nextTimestamp()));
        assert($cal->nextTimestamp() > $cal->timestamp());

        // parseTimestamp
        assert($cal->parseTimestamp(1543813203)['data-timestamp'] === 1543813200);
        assert($cal->parseTimestamp(1543813200)['data-timestamp'] === 1543813200);

        // setFormat
        assert($cal->setFormat('dateToDay') === $cal);

        // format
        assert($cal->format() === 'dateToDay');

        // isSelected

        // setSelected
        assert($cal->setSelected(Base\Datetime::mk(2018,12,4)) === $cal);

        // selected
        assert($cal->selected() === [1543899600]);

        // structure
        assert(count($cal->structure()) === 6);

        // output
        assert(strlen($cal->output()) > 3600);

        // head

        // body

        // tableHead

        // tableBody

        return true;
    }
}
?>