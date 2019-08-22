<?php
declare(strict_types=1);
namespace Quid\Main\Map;
use Quid\Base;

// _flash
trait _flash
{
	// onPrepareValueSet
	// les valeurs sont cast sur set
	protected function onPrepareValueSet($return) 
	{
		$return = Base\Obj::cast($return);
		
		return $return;
	}
	
	
	// get
	// retourne une valeur d'une clé dans la map
	// efface la valeur après lecture
	public function get($key)
	{
		$return = null;
		
		if($this->exists($key))
		{
			$return = parent::get($key);
			$this->unset($key);
		}
		
		return $return;
	}
	
	
	// gets
	// retourne plusieurs valeurs de clés dans la map
	// efface les valeurs après lecture
	public function gets(...$keys):array
	{
		$return = array();
		
		if($this->exists(...$keys))
		{
			$return = parent::gets(...$keys);
			$this->unset(...$keys);
		}
		
		return $return;
	}
}
?>