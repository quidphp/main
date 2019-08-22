<?php
declare(strict_types=1);
namespace Quid\Main\File;
use Quid\Main;
use Quid\Base;

// _email
trait _email
{
	// trait
	use Main\_email;
	
	
	// config
	public static $configFileEmail = [
		'mailer'=>[]
	];
	
	
	// contentType
	// retourne le type de contenu du email, retourne string
	public function contentType():string
	{
		return $this->readGet('contentType');
	}
	
	
	// subject
	// retourne le sujet du email
	public function subject():string
	{
		return $this->readGet('subject');
	}
	
	
	// body
	// retourne le body du email
	public function body():string
	{
		return $this->readGet('body');
	}
	
	
	// serviceMailer
	// retourne l'objet mailer à utiliser pour envoyer le courriel
	// peut envoyer une exception
	public static function serviceMailer($key=null):Main\ServiceMailer
	{
		$return = null;
		
		if(is_string($key) && array_key_exists($key,static::$config['mailer']))
		$return = static::$config['mailer'][$key];
		else
		$return = current(static::$config['mailer']);
		
		if(empty($return))
		static::throw('noMailerAtKey',$key);
		
		return $return;
	}
	
	
	// setMailer
	// permet de lier un objet mailer à la classe
	// key doit être une string
	public static function setMailer(string $key,Main\ServiceMailer $value):void 
	{
		static::$config['mailer'][$key] = $value;
		
		return;
	}
}
?>