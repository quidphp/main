<?php
declare(strict_types=1);
namespace Quid\Main\Test;
use Quid\Main;
use Quid\Base;

// extenders
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
		\assert($s->isNotEmpty());

		// onPrepareThis

		// is

		// set
		$s->set('ex2',$ex2);
		\assert($s->count() === 2);
		
		return true;
	}
}
?>