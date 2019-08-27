<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main;

// insert
class Insert extends Map
{
	// config
	public static $config = [];


	// allow
	protected static $allow = ['set','push','unshift','serialize','clone']; // méthode permises


	// set
	// comme set, mais vérifie que la clé n'existe pas
	public function set($key,$value):parent
	{
		if($key !== null && $this->exists($key))
		static::throw('cannotUpdateExistingKey');

		return parent::set($key,$value);
	}
}
?>