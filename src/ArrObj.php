<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main;

// arrObj
abstract class ArrObj extends Root implements \ArrayAccess, \Countable, \Iterator
{
	// trait
	use _arrObj;


	// config
	public static $config = [];
}
?>