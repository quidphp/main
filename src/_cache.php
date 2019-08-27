<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main;
use Quid\Base;

// _cache
trait _cache
{
	// cache
	protected $cache = []; // tableau avec les éléments mis en cache dans l'objet


	// cache
	// retourne la cache si existante, sinon crée la cache à partir de la callable sans argument
	public function cache($key,\Closure $closure=null,bool $cache=true)
	{
		$return = null;

		if($cache === true)
		{
			$key = Base\Obj::cast($key);
			$key = Base\Str::cast($key,'-');

			if(is_string($key) && strlen($key))
			{
				if($closure === null)
				$return = Base\Arr::unsetRef($key,$this->cache);

				else
				{
					if(Base\Arr::keyExists($key,$this->cache))
					$return = Base\Arr::get($key,$this->cache);

					else
					{
						$return = $closure();
						Base\Arr::setRef($key,$return,$this->cache);
					}
				}
			}
		}

		elseif(!empty($closure))
		$return = $closure();

		return $return;
	}


	// emptyCache
	// vide le tableau de cache
	public function emptyCache():self
	{
		$this->cache = [];

		return $this;
	}


	// allCache
	// retourne toute la cache
	public function allCache():array
	{
		return $this->cache;
	}
}
?>