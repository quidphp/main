<?php
declare(strict_types=1);
namespace Quid\Main\Contract;

// log
interface Log
{
	// log
	// crée une nouvelle entrée du log maintenant
	public static function log(...$values):?self;
	
	
	// logOnCloseDown
	// queue la création d'une nouvelle entrée du log au closeDown
	public static function logOnCloseDown(...$values):void;
	
	
	// logTrim
	// trim le nombre de log par une valeur paramétré
	public static function logTrim():?int;
}
?>