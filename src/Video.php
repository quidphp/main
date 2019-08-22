<?php
declare(strict_types=1);
namespace Quid\Main;
use Quid\Base;

// video
class Video extends Map
{
	// trait
	use _option;
	
	
	// config
	public static $config = array(
		'option'=>array(
			'name'=>'name',
			'date'=>'date',
			'description'=>'description',
			'absolute'=>'absolute',
			'thumbnail'=>'thumbnail',
			'html'=>'html')
	);
	
	
	// map
	protected static $allow = array('overwrite','serialize','clone'); // méthodes permises
	
	
	// construct
	// créer l'objet video, le premier argument doit être un tableau ou une chaîne json
	public function __construct($value,?array $option=null) 
	{
		if(is_string($value))
		$value = Base\Json::decode($value);
		
		if(is_array($value))
		$this->overwrite($value);
		
		else
		static::throw('requires','jsonStringOrArray');
		
		$this->option($option);
		
		return;
	}
	
	
	// toString
	// affiche l'objet comme string, retourne le html
	public function __toString():string
	{
		return $this->html();
	}
	
	
	// grab
	// retourne une valeur de la map, en utilisation la clé tel que défini dans option
	public function grab(string $key) 
	{
		$return = null;
		$realKey = $this->getOption($key);
		
		if(is_string($realKey))
		$return = $this->get($realKey);
		
		elseif(static::classIsCallable($realKey))
		$return = $realKey($this);

		return $return;
	}
	
	
	// name
	// retourne le nom de la vidéo
	// est facultatif
	public function name():?string 
	{
		$return = null;
		$name = $this->grab('name');
		
		if(is_string($name))
		$return = $name;
		
		return $return;
	}
	
	
	// date
	// retourne la date de la vidéo
	// possible de spécifier un format
	// est facultatif
	public function date($format=null) 
	{
		$return = null;
		$date = $this->grab('date');
		
		if(is_string($date))
		{
			$date = Base\Date::time($date,'sql');
			
			if(is_int($date))
			{
				$return = $date;
				
				if(is_scalar($format))
				$return = Base\Date::format($format,$return);
			}
		}
		
		return $return;
	}
	
	
	// description
	// retourne la description de la vidéo
	// possible de spécifier une longueur d'excerpt
	// est facultatif
	public function description(?int $length=null):?string 
	{
		$return = null;
		$description = $this->grab('description');
		
		if(is_string($description))
		{
			$return = $description;
			
			if(is_int($length))
			$return = Base\Str::excerpt($length,$return);
		}
		
		return $return;
	}
	
	
	// absolute
	// retourne le lien absolue de la vidéo
	// est obligatoire
	public function absolute():string 
	{
		$return = null;
		$absolute = $this->grab('absolute');
		
		if(is_string($absolute))
		$return = Base\Uri::absolute($absolute);
		
		return $return;
	}
	
	
	// thumbnail
	// retourne le lien vers le thumbnail de la vidéo
	// est facultatif
	public function thumbnail():?string 
	{
		$return = null;
		$thumbnail = $this->grab('thumbnail');
		
		if(is_string($thumbnail))
		$return = Base\Uri::absolute($thumbnail);
		
		return $return;
	}
	
	
	// html
	// retourne le code html de la vidéo
	// est obligatoire
	public function html():string 
	{
		$return = null;
		$html = $this->grab('html');
		
		if(is_string($html))
		$return = $html;
		
		return $return;
	}
	
	
	// input
	// retourne la string input si existante
	public function input():?string 
	{
		return $this->get('input');
	}
}
?>