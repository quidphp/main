<?php
declare(strict_types=1);
namespace Quid\Main;
use Quid\Base;

// localization
class Localization extends Map
{
	// config
	public static $config = [];
	
	
	// map
	protected static $allow = ['overwrite','jsonSerialize','serialize','clone']; // méthodes permises

	
	// construct
	// construit l'objet de localization
	// une string json ou un array doit être fourni
	public function __construct($value) 
	{
		if(\is_string($value))
		$value = Base\Json::decode($value);
		
		if(\is_array($value))
		$this->overwrite($value);
		
		else
		static::throw('requires','jsonStringOrArray');
		
		return;
	}
	
	
	// toString
	// affiche l'objet comme string, retourne la string input
	public function __toString():string
	{
		return $this->input();
	}
	
	
	// onPrepareReplace
	// prépare le tableau de remplacement en vue d'un overwrite
	// une exception sera envoyé si le tableau n'est pas dans le bon format
	public function onPrepareReplace($value) 
	{
		$return = null;
		
		if(\is_array($value) && Base\Arr::keysExists(['lat','lng','countryCode','input'],$value))
		{
			if(!(\is_numeric($value['lat']) && \is_numeric($value['lng'])))
			static::throw('invalidLatLng');
			
			if(!(\is_string($value['countryCode']) && \strlen($value['countryCode']) === 2))
			static::throw('invalidCountryCode');
			
			if(\is_string($value['input']))
			$return = $value;
		}
		
		if(!\is_array($return))
		static::throw('invalidFormat');
		
		return $return;
	}
	
	
	// inUsa
	// retourne vrai si le pays de la localization est USA
	public function inUsa() 
	{
		return (\strtoupper($this->countryCode()) === 'US')? true:false;
	}
	
	
	// lat
	// retourne la valeur lat sous forme de float
	public function lat():float 
	{
		return Base\Number::cast($this->get('lat'));
	}
	
	
	// lng
	// retourne la valeur lng sous forme de float
	public function lng():float 
	{
		return Base\Number::cast($this->get('lng'));
	}
	
	
	// latLng
	// retourne le tableau latlng
	public function latLng():array
	{
		return ['lat'=>$this->lat(),'lng'=>$this->lng()];
	}
	
	
	// input
	// retourne la string input si existante
	public function input():?string 
	{
		return $this->get('input');
	}
	
	
	// countryCode
	// retourne le code de pays
	public function countryCode():string 
	{
		return $this->get('countryCode');
	}
}
?>