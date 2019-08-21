<?php
declare(strict_types=1);
namespace Quid\Main\Test;
use Quid\Main;
use Quid\Base;

// extender
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