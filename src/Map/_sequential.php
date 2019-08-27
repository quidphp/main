<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/base/blob/master/LICENSE
 */

namespace Quid\Main\Map;
use Quid\Main;

// _sequential
trait _sequential
{
	// sequential
	// ramène les clés de la map séquentielle, numérique et en ordre
	public function sequential():Main\Map
	{
		$this->checkAllowed('sequential');
		$return = $this->onPrepareThis('sequential');
		$data =& $return->arr();
		$data = array_values($data);

		return $return;
	}
}
?>