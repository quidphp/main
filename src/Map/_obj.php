<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/base/blob/master/LICENSE
 */

namespace Quid\Main\Map;

// _obj
trait _obj
{
	// trait
	use _classeObj;


	// classeOrObj
	// retourne que le trait doit utilisé l'appelation d'objet
	public static function classeOrObj():string
	{
		return 'obj';
	}
}
?>