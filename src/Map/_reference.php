<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/base/blob/master/LICENSE
 */

namespace Quid\Main\Map;
use Quid\Main;
use Quid\Base;

// _reference
trait _reference
{
	// construct
	// construit l'objet et attribue la référence
	public function __construct(array &$value)
	{
		$this->data =& $value;

		return;
	}


	// onCheckArr
	// s'il y a is, fait une validation sur l'ensemble car l'original peut avoir changé
	protected function onCheckArr():Main\Map
	{
		if(!empty(static::$is) && !Base\Arr::validate(static::$is,$this->data))
		static::throw('onlyAccepts',static::$is);

		return $this;
	}
}
?>