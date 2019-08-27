<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main;
use Quid\Base;

// root
abstract class Root extends Base\Root implements \Serializable, \JsonSerializable
{
	// trait
	use _rootClone;


	// config
	public static $config = [];
}
?>