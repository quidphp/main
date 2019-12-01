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

// _queue
// trait that provides methods required for implementing queuing functionality to an object
trait _queue
{
    // onUnqueue
    // callback sur unqueue, permet par exemple d'effacer l'entrée après le unqueue
    final protected function onUnqueue():void
    {
        return;
    }


    // triggerUnqueueOnCloseDown
    // enregistre la méthode unqueue pour qu'elle s'éxécute au closeDown
    final public static function triggerUnqueueOnCloseDown(?int $limit=null,?int $timeLimit=null,?float $sleep=null):void
    {
        Base\Response::onCloseDown(function() use($limit,$timeLimit,$sleep) {
            static::triggerUnqueue($limit,$timeLimit,$sleep);
            return;
        });

        return;
    }


    // unqueue
    // lance le processus de unqueue
    // possible de mettre une limit, une limite de temps et un temps de sleep entre chaque unqueue
    // une exception est envoyé si l'objet ne supporte pas la méthode unqueue
    final public static function triggerUnqueue(?int $limit=null,?int $timeLimit=null,?float $sleep=null):?array
    {
        $return = null;
        $queues = static::getQueued($limit);

        if(!empty($queues))
        {
            $maxTime = (is_int($timeLimit))? (Base\Datetime::timestamp() + $timeLimit):null;

            foreach ($queues as $key => $obj)
            {
                if(method_exists($obj,'unqueue'))
                {
                    $return[$key] = $obj->unqueue();

                    $obj->onUnqueue();

                    if(is_numeric($sleep))
                    Base\Response::sleep($sleep);

                    if(is_int($maxTime) && Base\Datetime::timestamp() > $maxTime)
                    break;
                }

                else
                static::throw('objectRequiresMethod','unqueue');
            }
        }

        return $return;
    }
}
?>