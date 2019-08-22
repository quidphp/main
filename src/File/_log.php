<?php
declare(strict_types=1);
namespace Quid\Main\File;
use Quid\Main;
use Quid\Base;

// _log
trait _log
{
	// trait
	use Main\_log, _storage;
	
	
	// config
	public static $configFileLog = array(
		'type'=>'dump',
		'deleteTrim'=>null
	);
	
	
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