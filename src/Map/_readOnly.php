<?php
declare(strict_types=1);
namespace Quid\Main\Map;
use Quid\Main;

// _readOnly
trait _readOnly
{
	// dynamique
	protected $readOnly = false; // si l'objet est présentement en mode read only
	
	
	// clone
	// ramène readOnly à false sur clone
	public function __clone() 
	{
		$this->readOnly(false);
		
		return;
	}
	
	
	// isReadOnly
	// retourne vrai si l'objet est en mode readOnly
	public function isReadOnly():bool 
	{
		return $this->readOnly;
	}
	
	
	// readOnly
	// active ou désactive le mode readOnly
	public function readOnly(bool $readOnly=true):Main\Map 
	{
		$this->readOnly = $readOnly;
		
		return $this;
	}
	
	
	// checkReadOnly
	// retourne l'objet si l'objet n'est pas readOnly, sinon lance une exception
	protected function checkReadOnly():Main\Map 
	{
		if($this->isReadOnly())
		static::throw();
		
		return $this;
	}
	
	
	// checkAllowed
	// retourne l'objet si la méthode est permis, sinon lance une exception
	// exception non envoyé si c'est pour jsonSerialize, serialize ou clone
	protected function checkAllowed(string ...$values):Main\Map
	{
		foreach ($values as $value) 
		{
			if(!in_array($value,static::allowedReadOnlyMethods()))
			$this->checkReadOnly();
			
			if(!$this->isAllowed($value))
			static::throw($value);
		}
		
		return $this;
	}
	
	
	// allowedReadOnlyMethods
	// retourne les méthodes permises même si readOnly est true
	public static function allowedReadOnlyMethods():array
	{
		return ['filter','jsonSerialize','serialize','clone'];
	}
}
?>