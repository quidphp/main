<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/base/blob/master/LICENSE
 */

namespace Quid\Main;

// std
class Std extends Map
{
	// trait
	use Map\_arr;
	use Map\_basic;
	use Map\_count;
	use Map\_readOnly;
	use Map\_sort;
	use Map\_sequential;
	use Map\_filter;
	use Map\_map;
	
	
	// config
	public static $config = [];
}
?>