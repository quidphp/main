<?php
declare(strict_types=1);
namespace Quid\Main;
use Quid\Base;

// serviceRequest
abstract class ServiceRequest extends Service
{
	// config
	public static $config = [
		'target'=>null, // cible du service
		'option'=>[ // option par défaut pour request
			'ping'=>2,
			'responseCode'=>200]
	];
	
	
	// target
	// retourne la target du service
	// envoie une exception si vide
	public static function target(?array $replace=null):string 
	{
		$return = static::$config['target'] ?? null;
		
		if(is_string($return) && !empty($return) && !empty($replace))
		{
			$replace = Base\Arr::keysWrap('%','%',$replace);
			$return = Base\Str::replace($replace,$return);
		}
		
		return $return;
	}
	
	
	// makeRequest
	// retourne un nouvel objet requête
	// utilise la classe requête dans requestClass et les options dans requestOption
	// méthode protégé
	protected static function makeRequest($value=null,array $option):Request 
	{
		$return = null;
		$class = static::requestClass();
		
		if(empty($option['userAgent']))
		$option['userAgent'] = static::userAgent();
		
		$return = new $class($value,$option);
		
		return $return;
	}
	
	
	// requestClass
	// retourne la classe à utiliser pour request
	public static function requestClass():string 
	{
		return Request::getOverloadClass();
	}
	
	
	// userAgent
	// retourne le userAgent à utiliser s'il n'est pas spécifié dans option
	public static function userAgent():string 
	{
		return 'QUID/'.Base\Server::quidVersion();
	}
}
?>