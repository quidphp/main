<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/base/blob/master/LICENSE
 */

namespace Quid\Main;
use Quid\Base;

// requestHistory
class RequestHistory extends Map
{
	// trait
	use Map\_count;
	use Map\_nav;
	
	
	// config
	public static $config = [
		'extra'=>['redirectable'=>true] // clé extra à utiliser pour générer la string de requête
	];


	// map
	protected static $is = 'string'; // validate pour l'objet
	protected static $allow = ['unshift','push','remove','unsetAfterCount','empty','serialize']; // méthodes permises
	protected static $after = ['unsetAfterCount'=>50]; // maximum de requête conservés


	// onPrepareValue
	// préparation spéciale si la valeur est une instance de request
	// permet de retourner une valeur identique dans l'objet sans considérer le timestamp
	protected function onPrepareValue($return)
	{
		if($return instanceof Request)
		{
			$extra = static::extra(true);
			$return = $return->str($extra);

			if(is_string($return) && !empty($return))
			{
				$r = Base\Http::arr($return,$extra);
				unset($r['timestamp']);

				foreach ($this->arr() as $value)
				{
					$v = Base\Http::arr($value,$extra);

					if($r === Base\Arr::keyStrip('timestamp',$v))
					$return = $value;
				}
			}
		}

		return $return;
	}


	// onPrepareReturns
	// prépare le retour pour indexes, gets et slice
	protected function onPrepareReturns(array $array):self
	{
		$return = new static();

		foreach ($array as $key => $value)
		{
			$return->push($value);
		}

		return $return;
	}


	// cast
	// cast de l'historique, retourne le count
	public function _cast()
	{
		return $this->count();
	}


	// hasUri
	// retourne vrai si l'uri est dans l'objet
	public function hasUri($value):bool
	{
		$return = false;

		$uris = $this->absolute();
		if(is_string($value) && in_array($value,$uris,true))
		$return = true;

		return $return;
	}


	// hasCurrentUri
	// retourne vrai si l'uri courante est dans l'objet
	public function hasCurrentUri():bool
	{
		return $this->hasUri(Base\Request::absolute());
	}


	// add
	// ajoute une requête à l'historique
	public function add(Request $value):self
	{
		return $this->unshift($this->onPrepareValue($value));
	}


	// addUnique
	// ajoute une requête à l'historique seulement si elle n'existe pas déjà
	public function addUnique(Request $value):self
	{
		$this->remove($value);
		$this->add($value);

		return $this;
	}


	// previous
	// retourne la dernière uri dans l'historique qui n'est pas la courante
	// si hasExtra est true, la requête doit contenir les clés->valeurs de extra
	public function previous(bool $hasExtra=true):?array
	{
		$return = null;
		$extra = static::extra();

		foreach ($this->all() as $key => $value)
		{
			if($value['absolute'] !== Base\Request::absolute())
			{
				if($hasExtra === false || Base\Arr::hasSlices($extra,$value))
				{
					$return = $value;
					break;
				}
			}
		}

		return $return;
	}


	// previousRequest
	// retourne la requête précédente
	public function previousRequest(bool $hasExtra=true):?Request
	{
		$return = null;
		$previous = $this->previous($hasExtra);

		if(!empty($previous))
		$return = Request::newOverload($previous);

		return $return;
	}


	// absolute
	// retourne toutes les uris absoluts de requête dans un tableau
	public function absolute():array
	{
		$return = [];

		foreach ($this->all() as $key => $value)
		{
			$return[$key] = $value['absolute'];
		}

		return $return;
	}


	// request
	// retourne un tableau avec tous les objets requêtes
	public function request():array
	{
		$return = [];

		foreach ($this->all() as $key => $value)
		{
			$return[$key] = Request::newOverload($value);
		}

		return $return;
	}


	// all
	// retourne toutes les requête
	public function all():array
	{
		$return = [];
		$extra = static::extra(true);

		foreach ($this->arr() as $key => $value)
		{
			$arr = Base\Http::arr($value,$extra);

			if(static::isArrayValid($arr))
			$return[$key] = $arr;
		}

		return $return;
	}


	// extra
	// retourne le tableau extra
	public static function extra(bool $keys=false):array
	{
		$return = static::$config['extra'] ?? [];

		if($keys === true && !empty($return))
		$return = array_keys($return);

		return $return;
	}


	// isArrayValid
	// retourne vrai si le tableau est valide pour créer une requête
	public static function isArrayValid($value):bool
	{
		return (is_array($value) && Base\Arr::keysExists(['absolute','method','timestamp'],$value))? true:false;
	}
}
?>