<?php
declare(strict_types=1);
namespace Quid\Main;
use Quid\Base;

// _log
trait _log
{
	// queue
	public static $queue = 0; // nombre de logs queues pour la classe
	
	
	// logOnCloseDown
	// queue l'insertion d'une nouvelle entrée du log au closeDown
	// lance logTrim si c'est le dernier élément de la queue
	public static function logOnCloseDown(...$values):void
	{
		Base\Response::onCloseDown(function() use($values) {
			static::log(...$values);
			static::$queue--;
			
			if(static::$queue === 0)
			static::logTrim();
			
			return;
		});
		
		static::$queue++;
		
		return;
	}
}
?>