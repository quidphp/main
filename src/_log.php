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
    protected static bool $logHad = false; // conserve s'il y a déjà eu un log pour la classe


    // logHad
    // indique que la classe a loggé quelque chose
    final protected static function logHad():void
    {
        static::$logHad = true;
    }


    // logShouldAfter
    // retourne vrai s'il faut trim le log
    final protected static function logShouldAfter():bool
    {
        return static::$queue === 0 && static::$logHad === true;
    }


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
    // fait un log au close down
    final public static function logCloseDown(...$values):void
    {
        static::logClosureCloseDown(function() use($values) {
            static::log(...$values);
        });
    }


    // logClosureCloseDown
    // queue l'insertion d'une nouvelle entrée du log au closeDown
    // lance logTrim si c'est le dernier élément de la queue
    protected static function logClosureCloseDown(\Closure $closure):void
    {
        Base\Response::onCloseDown(function() use($closure) {
            $closure();

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
        if(static::logShouldAfter())
        static::logTrim();
    }
}
?>