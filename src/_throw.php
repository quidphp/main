<?php
declare(strict_types=1);
namespace Quid\Main;
use Quid\Base;

// _throw
trait _throw
{
	// throw
	// lance une nouvelle exception
	// ajoute la classe et méthode statique appelant au début du message de l'exception
	// méthode protégé
	protected static function throw(...$values):void 
	{
		$class = Exception::getOverloadClass();
		static::throwCommon($class,$values);
		
		return;
	}
	
	
	// catchable
	// lance une exception attrapable
	// ajoute la classe et méthode statique appelant au début du message de l'exception
	// la différence avec throw est qu'il y a maintenant un tableau action en premier argument, qui permet de définir le traitement lors du onCatched
	// méthode protégé
	protected static function catchable(?array $option=null,...$values):void 
	{
		$class = CatchableException::getOverloadClass();
		static::throwCommon($class,$values,$option);
		
		return;
	}
	
	
	// throwCommon
	// méthode commune utilisé pour envoyer des exceptions
	protected static function throwCommon(string $class,array $values,?array $option=null):void
	{
		$source = (static::class !== self::class)? static::class:null;
		$message = Base\Exception::classFunction(Base\Debug::traceIndex(3),$source,$values);
		throw new $class($message,null,$option,...$values);
		
		return;
	}
}
?>