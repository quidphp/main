<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/base/blob/master/LICENSE
 */

namespace Quid\Main\File;
use Quid\Main;

// _log
trait _log
{
	// trait
	use Main\_log;
	use _storage;
	
	
	// config
	public static $configFileLog = [
		'type'=>'dump',
		'deleteTrim'=>null
	];


	// log
	// crée une nouvelle entrée du log maintenant
	public static function log(...$values):?Main\Contract\Log
	{
		return static::storage(...$values);
	}


	// logTrim
	// trim le nombre de log dans le chemin par une valeur paramétré
	public static function logTrim():?int
	{
		return static::storageTrim(static::$config['deleteTrim']);
	}
}
?>