<?php
declare(strict_types=1);
namespace Quid\Main\Map;
use Quid\Main;
use Quid\Base; 

// _count
trait _count
{
	// unsetAfterCount
	// enlève les entrées après un certain nombre
	public function unsetAfterCount(int $count):Main\Map
	{
		$this->checkAllowed('unsetAfterCount');
		$return = $this->onPrepareThis('unsetAfterCount');
		$data =& $return->arr();
		$data = Base\Arr::unsetAfterCount($count,$data);
		
		if(empty(static::$after['unsetAfterCount']))
		$return->checkAfter();
		
		return $return;
	}
}
?>