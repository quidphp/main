<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/base/blob/master/LICENSE
 */

namespace Quid\Main\Map;
use Quid\Main;

// _filter
trait _filter
{
	// filter
	// permet de filtrer l'objet à partir d'une condition à ce moment seul les entrées true sont gardés
	public function filter($condition,...$args):Main\Map
	{
		$this->checkAllowed('filter');
		$return = $this->onPrepareThis('filter');
		$data =& $return->arr();

		foreach ($return->arr() as $key => $value)
		{
			if($return->filterCondition($condition,$key,$value,...$args) === false)
			unset($data[$key]);
		}

		return $return->checkAfter();
	}
}
?>