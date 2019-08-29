<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main\Contract;

// log
// interface to detail the methods required for implementing logging functionality to an object
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