<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main\Map;
use Quid\Main;

// _cloner
trait _cloner
{
	// onPrepareThis
	// l'objet est cloner avant chaque modification
	protected function onPrepareThis(string $method):Main\Map
	{
		return clone $this;
	}
}
?>