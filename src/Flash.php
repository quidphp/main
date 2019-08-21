<?php
declare(strict_types=1);
namespace Quid\Main;

// flash
class Flash extends Map
{
	// trait
	use Map\_flash;
	
	
	// config
	public static $config = [];
	
	
	// map
	protected static $allow = ['set','unset','serialize','empty']; // méthodes permises
}
?>