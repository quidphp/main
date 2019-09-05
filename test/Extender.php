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

		// onAddNamespace

		// add

		// addNamespace

		// isExtended
		assert(!$ex->isExtended('test'));

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