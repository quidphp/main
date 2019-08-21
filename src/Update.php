<?php
declare(strict_types=1);
namespace Quid\Main;

// update
class Update extends Map
{
	// config
	public static $config = [];
	
	
	// allow
	protected static $allow = ['set','serialize','clone']; // méthode permises
	
	
	// set
	// comme set, mais vérifie que la clé existe
	public function set($key,$value):parent
	{
		if($key === null || !$this->exists($key))
		static::throw('cannotInsertNewKey');
		
		return parent::set($key,$value);
	}
}
?>