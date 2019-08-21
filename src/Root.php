<?php
declare(strict_types=1);
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