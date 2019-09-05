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

// flash
// class for testing Quid\Main\Flash
class Flash extends Base\Test
{
	// trigger
	public static function trigger(array $data):bool
	{
		// construct
		$f = new Main\Flash();

		// map
		$f['test'] = 2;
		assert($f->get('test') === 2);
		assert($f->get('test') === null);
		$f['test'] = 2;
		$f['bla'] = 3;
		assert($f->gets('bla','test') === ['bla'=>3,'test'=>2]);
		$f['test'] = 2;
		$f['bla'] = 3;
		assert(isset($f['test']));
		assert($f['test'] === 2);
		assert(!isset($f['test']));
		assert($f['bla'] === 3);
		assert($f->isEmpty());
		$f['test'] = 2;
		assert($f->keys() === ['test']);
		$f['test3'] = 2;
		$f['test4'] = 2;
		assert($f instanceof Main\Flash);

		return true;
	}
}
?>