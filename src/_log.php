<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 * Readme: https://github.com/quidphp/main/blob/master/README.md
 */

namespace Quid\Main;
use Quid\Base;

// _log
// trait that provides a required method to allow logging with the object
trait _log
{
    // queue
    public static $queue = 0; // nombre de logs queues pour la classe


    // logOnCloseDown
    // queue l'insertion d'une nouvelle entrée du log au closeDown
    // lance logTrim si c'est le dernier élément de la queue
    final public static function logOnCloseDown(...$values):void
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