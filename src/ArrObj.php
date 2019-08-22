<?php
declare(strict_types=1);
namespace Quid\Main;

// arrObj
abstract class ArrObj extends Root implements \ArrayAccess, \Countable, \Iterator
{
	// trait
	use _arrObj;
	
	
	// config
	public static $config = array();
}
?>