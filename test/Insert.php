<?php
declare(strict_types=1);
namespace Quid\Main\Test;
use Quid\Main;
use Quid\Base;

// insert
class Insert extends Base\Test
{
	// trigger
	public static function trigger(array $data):bool
	{
		// construct
		$i = new Main\Insert(['ok'=>2]);
		
		// map
		\assert($i->set('ok2',3)->isCount(2));
		\assert($i->set(null,'what')->isCount(3));
		\assert($i->push('lol','lol2')->unshift('lolz','lolz2')->isCount(7));
		
		return true;
	}
}
?>