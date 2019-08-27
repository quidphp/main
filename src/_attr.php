<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main;
use Quid\Base;

// _attr
trait _attr
{
	// dynamique
	protected $attr = []; // propriété pour conserver le tableau des attributs de l'objet


	// makeAttr
	// conserve une copie des attributs
	protected function makeAttr(array $value):self
	{
		$this->attr = $value;

		return $this;
	}


	// attr
	// retourne le tableau d'attribut ou une valeur du tableau attr
	public function attr($key=null)
	{
		$return = null;

		if($key !== null)
		$return = Base\Arrs::get($key,$this->attr);

		else
		$return = $this->attr;

		return $return;
	}


	// attrCall
	// retourne un attribut
	// si la valeur est callable, utilise base\call withObj (donc this est lié à la closure)
	public function attrCall($key,...$args)
	{
		$return = $this->attr($key);

		if(static::classIsCallable($return))
		$return = Base\Call::withObj($this,$return,...$args);

		return $return;
	}


	// attrNotEmpty
	// retourne vrai si l'attribut n'est pas vide
	public function attrNotEmpty($key):bool
	{
		return (Base\Validate::isReallyEmpty($this->attr($key)))? false:true;
	}


	// setAttr
	// permet de changer la valeur d'un attribut
	public function setAttr($key,$value):self
	{
		Base\Arrs::setRef($key,$value,$this->attr);

		return $this;
	}
}
?>