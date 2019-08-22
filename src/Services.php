<?php
declare(strict_types=1);
namespace Quid\Main;
use Quid\Base;

// services
class Services extends Map
{
	// trait
	use _inst, Map\_sort, Map\_readOnly, Map\_obj;
	
	
	// config
	public static $config = [];
	
	
	// map
	protected static $allow = ['set','unset','remove','sort','clone']; // méthodes permises
	protected static $sortDefault = 'getKey'; // défini la méthode pour sort par défaut
	
	
	// construct
	// construit l'objet services
	public function __construct(?array $value=null) 
	{
		if(is_array($value))
		{
			$this->sets($value);
			$this->sortDefault();
		}
		
		return;
	}
	
	
	// set
	// ajoute un rôle à l'objet roles
	// le init config de role est lancé avant l'ajout
	public function set($key,$value):parent 
	{
		if(!is_string($key))
		static::throw('onlyStringKeyAllowed');
		
		if($this->exists($key))
		static::throw('alreadyIn',$key);
		
		$class = null;
		$args = [];
		
		if(is_string($value))
		$class = $value;
		
		elseif(is_array($value))
		{
			$class = current($value);
			$args = Base\Arr::spliceFirst($value);
			$args = array_values($args);
		}
		
		if(!is_string($class) || !is_subclass_of($class,Service::class,true))
		static::throw('notSubClassOfService',$class);
		
		$value = new $class($key,...$args);
		parent::set($key,$value);
		
		return $this;
	}
	
	
	// pairCastClean
	// retourne un tableau avec les valeurs de cast clean
	// donc les valeurs vides ne sont pas retournés
	public function pairCastClean():array
	{
		return Base\Arr::clean($this->pair('cast'));
	}
}
?>