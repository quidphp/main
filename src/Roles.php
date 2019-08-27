<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/base/blob/master/LICENSE
 */

namespace Quid\Main;

// roles
class Roles extends Extender
{
	// trait
	use _inst;
	use Map\_classe;
	use Map\_sort;
	use Map\_readOnly;


	// config
	public static $config = [
		'option'=>[
			'methodIgnore'=>'isIgnored',
			'subClass'=>Role::class]
	];


	// map
	protected static $allow = ['set','unset','remove','filter','sort','serialize','clone']; // méthodes permises
	protected static $sortDefault = 'permission'; // défini la méthode pour sort par défaut


	// onPrepareKey
	// prepare une clé pour les méthodes qui soumette une clé
	protected function onPrepareKey($return)
	{
		if((is_string($return) && class_exists($return,false)) || is_object($return))
		{
			if(is_a($return,Role::class,true))
			$return = static::getKey($return);
		}

		return $return;
	}


	// getObject
	// retourne null ou l'objet du role
	public function getObject($value):?Role
	{
		$return = null;
		$value = $this->get($value);

		if(is_string($value))
		$return = new $value();

		return $return;
	}


	// nobody
	// retorne le premier role nobody, en objet
	public function nobody():?Role
	{
		$return = null;
		$value = $this->first(['isNobody'=>true]);

		if(is_string($value))
		$return = new $value();

		return $return;
	}


	// getKey
	// retourne la clé à utiliser pour la map
	public static function getKey($value)
	{
		return $value::permission();
	}
}

// config
Roles::__config();
?>