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

// extenders
// class for testing Quid\Main\Extenders
class Extenders extends Base\Test
{
	// trigger
	public static function trigger(array $data):bool
	{
		// prepare
		$ex = new Main\Extender(__NAMESPACE__);
		$ex2 = new Main\Extender("Quid\Base");

		// construct
		$s = new Main\Extenders(['ex'=>$ex]);
		assert($s->isNotEmpty());

		// onPrepareThis

		// is

		// set
		$s->set('ex2',$ex2);
		assert($s->count() === 2);

		return true;
	}
}
?>