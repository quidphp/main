<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main\Map;
use Quid\Main;
use Quid\Base;

// _map
trait _map
{
	// map
	// permet d'utiliser une callable pour changer les valeurs de l'objet
	// la nouvelle valeur est passé dans la méthode set
	public function map(callable $map,...$args):Main\Map
	{
		$this->checkAllowed('map');
		$return = $this->onPrepareThis('map');

		foreach ($this->arr() as $key => $value)
		{
			$new = Base\Call::withObj($return,$map,$value,$key,...$args);

			if($new !== $value)
			$return->set($key,$new);
		}

		return $return;
	}
}
?>