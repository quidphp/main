<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main;
use Quid\Base;

// _option
// trait that grants methods to work with the dynamic property option
trait _option
{
	// option
	protected $option = []; // tableau des options


	// isOption
	// retourne vrai si l'option existe
	public function isOption($key):bool
	{
		return Base\Arrs::keyExists($key,$this->option);
	}


	// getOption
	// retourne une option du tableau d'option
	public function getOption($key)
	{
		return Base\Arrs::get($key,$this->option);
	}


	// getOptionCall
	// retourne une option du tableau d'option
	// si la valeur est callable, utilise base\call withObj (donc this est lié à la closure)
	public function getOptionCall($key,...$args)
	{
		$return = $this->getOption($key);

		if(static::classIsCallable($return))
		$return = Base\Call::withObj($this,$return,...$args);

		return $return;
	}


	// setOption
	// ajoute ou change une option dans le tableau d'option
	public function setOption($key,$value):self
	{
		Base\Arrs::setRef($key,$value,$this->option);

		return $this;
	}


	// unsetOption
	// enlève une option du tableau d'option
	public function unsetOption($key):self
	{
		Base\Arrs::unsetRef($key,$this->option);

		return $this;
	}


	// option
	// merge les options
	// merge avec les défauts si vide
	// seules les options de config non null sont ajoutés dans l'objet
	// retourne les options
	public function option(?array $value=null):array
	{
		if(empty($this->option))
		$this->loadOption();

		if($value !== null)
		$this->option = Base\Arrs::replace($this->option,$value);

		return $this->option;
	}


	// loadOption
	// méthode appelé lorsque le tableau option est vide
	protected function loadOption():self
	{
		if(property_exists($this,'config') && !empty(static::$config['option']) && is_array(static::$config['option']))
		$this->option = static::$config['option'];

		return $this;
	}
}
?>