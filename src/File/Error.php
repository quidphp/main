<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main\File;
use Quid\Main;

// error
// class for an error storage file
class Error extends Dump implements Main\Contract\Log, Main\Contract\FileStorage
{
    // trait
    use _log;


    // config
    protected static array $config = [
        'deleteTrim'=>50
    ];


    // isStorageDataValid
    // retourne vrai si les datas fournis sont valides pour logError
    final public static function isStorageDataValid(...$values):bool
    {
        return !empty($values) && $values[0] instanceof Main\Error;
    }


    // storageData
    // retourne les données à mettre dans le fichier logError
    final public static function storageData(...$values)
    {
        return (!empty($values[0]))? $values[0]->toArray():[];
    }


    // storageFilename
    // retourne le filename de l'error, par défaut utilise le id de l'erreur
    final public static function storageFilename(...$values):string
    {
        return (static::isStorageDataValid(...$values))? $values[0]->basename(static::$config['inc']):null;
    }
}

// init
Error::__init();
?>