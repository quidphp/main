<?php
declare(strict_types=1);
namespace Quid\Main;

// std
class Std extends Map
{
	// trait
	use Map\_arr, Map\_basic, Map\_count, Map\_readOnly, Map\_sort, Map\_sequential, Map\_filter, Map\_map;
	
	
	// config
	public static $config = array();
}
?>