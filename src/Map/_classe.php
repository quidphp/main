<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main\Map;

// _classe
// trait that grants methods to work with a collection containing fully qualified class name strings
trait _classe
{
	// trait
	use _classeObj;


	// classeOrObj
	// retourne que le trait doit utilisé l'appelation de classe
	public static function classeOrObj():string
	{
		return 'classe';
	}
}
?>