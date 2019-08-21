<?php
declare(strict_types=1);
namespace Quid\Main\Map;
use Quid\Main;
use Quid\Base;

// _prepend
trait _prepend
{
	// set
	// ajoute ou change une clé valeur dans la map, accepte une clé null
	// si la clé n'existe pas elle est prepend
	public function set($key,$value):Main\Map
	{
		$this->checkAllowed('set');
		$return = $this->onPrepareThis('set');
		$key = $this->onPrepareKey($key);
		$value = $this->onPrepareValueSet($value);
		
		if($key === null)
		$return->unshift($value);
		
		elseif($return->exists($key) && $return->checkBefore(false,$value))
		Base\Arr::setRef($key,$value,$return->arr());
		
		elseif(Base\Arr::isKey($key))
		$return->prepend([$key=>$value]);
		
		return $return->checkAfter();
	}
	
	
	// add
	// raccourci pour unshift
	public function add(...$values):Main\Map
	{
		return $this->unshift(...$values);
	}
	
	
	// pend
	// raccourci pour prepend
	public function pend(...$values):Main\Map
	{
		return $this->prepend(...$values);
	}
}
?>