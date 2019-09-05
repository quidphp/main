<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/test/blob/master/LICENSE
 */

namespace Quid\Test\Main;
use Quid\Main;
use Quid\Base;

// timeout
// class for testing Quid\Main\Timeout
class Timeout extends Base\Test
{
	// trigger
	public static function trigger(array $data):bool
	{
		// prepare
		$t = new Main\Timeout();

		// isTimedOut
		assert(!$t->isTimedOut('none'));

		// isMaxed
		assert(!$t->isMaxed('none'));

		// set
		assert($t->set('test',['max'=>3,'timeout'=>400]) === $t);
		assert($t->set(['array',1,2,3],['max'=>3,'timeout'=>400])->isCount(2));
		assert($t->set(['array',1,2,3],['max'=>4])->get('array')['timeout'] = 600);
		assert($t->exists('test'));
		assert($t->exists(['array',1,2,3]));
		assert($t->sets(['test3'=>null])->isCount(3));

		// change
		assert($t->change('test',['timeout'=>399])->get('test')['timeout'] === 399);
		assert($t->change('test',['timeout'=>398])->get('test')['max'] === 3);

		// changes
		assert($t->changes(['test'=>['timeout'=>397]])->get('test')['timeout'] === 397);

		// getCount
		assert($t->getCount('test2') === null);
		assert($t->getCount('test') === 0);

		// setCount
		assert($t->setCount('test') === $t);
		assert($t->getCount('test',false) === 3);
		assert($t->getCount('test') === 3);
		assert($t->isTimedOut('test'));
		assert($t->setCount('test',8) === $t);
		assert($t->getCount('test',false) === 8);
		assert($t->isTimedOut('test'));
		assert($t->setCount('test',8) === $t);
		assert($t->isTimedOut('test'));
		assert($t->setCount('test',2) === $t);
		assert($t->getExpire('test') === null);
		assert(!$t->isTimedOut('test'));

		// block
		assert($t->block('test') === $t);
		assert($t->isTimedOut('test'));
		assert($t->resetTimestamp('test') === $t);
		assert(!$t->isTimedOut('test'));

		// addCount
		assert($t->addCount('test',1) === $t);
		assert(!$t->isTimedOut('test'));
		assert($t->addCount('test',2) === $t);
		assert($t->isTimedOut('test'));
		assert($t->addCount(['array',1,2,3]) === $t);
		assert($t->addCount(['array',1,2,3])->getCount(['array',1,2,3]) === 2);

		// increment
		assert($t->increment('test') === $t);
		assert($t->isTimedOut('test'));

		// resetCount
		assert($t->resetCount('test') === $t);
		assert(!$t->isTimedOut('test'));

		// getTimestamp
		assert($t->getTimestamp('test') === null);
		assert($t->addCount('test') === $t);
		assert(is_int($t->getTimestamp('test')));
		assert($t->addCount('test',8) === $t);
		assert(is_int($t->getTimestamp('test',false)));
		assert(is_int($t->getTimestamp('test')));

		// setTimestamp
		assert($t->setTimestamp('test',time() - 400) === $t);
		assert($t->getTimestamp('test') === null);

		// resetTimestamp
		assert($t->resetTimestamp('test') === $t);
		assert($t->getTimestamp('test') === null);

		// getExpire
		assert($t->addCount('test',8) === $t);
		assert($t->getExpire('test') === 397);

		// resetOne
		assert($t->resetOne('test') === $t);

		// resetAll
		assert($t->resetAll() === $t);

		// checkValueValid
		assert($t::checkValueValid(['timeout'=>8,'max'=>10]) === ['timeout'=>8,'max'=>10,'count'=>0,'timestamp'=>null]);

		// map
		assert($t->sets(['test'=>['timeout'=>398],'test3'=>['timeout'=>397]])->get('test3')['timeout'] === 397);
		assert(is_string(serialize($t)));
		assert($t->unset('test')->isCount(2));
		assert($t->empty() === $t);
		assert($t->isEmpty());

		return true;
	}
}
?>