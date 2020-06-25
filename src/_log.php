<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main;
use Quid\Base;

// _log
// trait that provides a required method to allow logging with the object
trait _log
{
    // queue
    protected static int $queue = 0; // nombre de logs queues pour la classe


    // logCloseDownCliNow
    // permet de logger maintenant si on est en cli, sinon envoie en closedown
    final public static function logCloseDownCliNow(...$values):void
    {
        if(Base\Server::isCli())
        static::log(...$values);

        else
        static::logCloseDown(...$values);
    }


    // logCloseDown
    // queue l'insertion d'une nouvelle entrée du log au closeDown
    // lance logTrim si c'est le dernier élément de la queue
    final public static function logCloseDown(...$values):void
    {
        Base\Response::onCloseDown(function() use($values) {
            static::log(...$values);

            if(static::$queue > 0)
            {
                static::$queue--;
                static::logAfter();
            }
        });

        static::$queue++;
    }


    // logAfter
    // code à lancer après la création du log
    public static function logAfter():void
    {
        if(static::$queue === 0)
        static::logTrim();
    }
}
?>