<?php
declare(strict_types=1);
namespace Quid\Main\Test;
use Quid\Main;
use Quid\Base;

// update
class Update extends Base\Test
{
	// trigger
	public static function trigger(array $data):bool
	{
		// construct
		$array = [1=>'test','bla'=>'OK'];
		$u = new Main\Update($array);
		
		// map
		\assert($u->set(1,'bla')->get(1) === 'bla');
		
		return true;
	}
}
?>