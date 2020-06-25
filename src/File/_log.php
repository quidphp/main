<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main\File;
use Quid\Base;
use Quid\Main;

// _log
// trait that grants methods to allow a file object to do logging
trait _log
{
    // trait
    use Main\_log;
    use _storage;


    // config
    protected static array $configFileLog = [
        'deleteTrim'=>null,
        'write'=>[
            'callback'=>[Base\Debug::class,'varGet']]
    ];


    // log
    // crée une nouvelle entrée du log maintenant
    // et lance le logTrim
    final public static function log(...$values):?Main\Contract\Log
    {
        $return = static::storage(...$values);
        static::logTrim();

        return $return;
    }


    // logTrim
    // trim le nombre de log dans le chemin par une valeur paramétré
    final public static function logTrim():?int
    {
        $return = null;
        $trim = static::$config['deleteTrim'];

        if(is_int($trim))
        $return = static::storageTrim($trim);

        return $return;
    }
}
?>