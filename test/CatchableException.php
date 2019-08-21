<?php
declare(strict_types=1);
namespace Quid\Main\Test;
use Quid\Main;
use Quid\Base;

// catchableException
class CatchableException extends Base\Test
{
	// trigger
	public static function trigger(array $data):bool
	{
		// construct
		$e = new Main\CatchableException('well');

		// exception
		\assert($e instanceof Main\Contract\Catchable);
		\assert($e->getCode() === 32);
		
		return true;
	}
}
?>