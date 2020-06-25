<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main\Contract;
use Quid\Main;

// queue
// interface to detail the methods required for implementing queuing functionality to an object
interface Queue
{
    // queue
    // créer une nouvelle entrée dans la queue
    public static function queue(...$values):?self;


    // getQueued
    // retourne un objet avec toutes les entrées queued
    public static function getQueued(?int $limit=null):?Main\Map;


    // triggerUnqueueOnCloseDown
    // enregistre la méthode unqueue pour qu'elle s'éxécute au closeDown
    public static function triggerUnqueueOnCloseDown(?int $limit=null,?int $timeLimit=null,?float $sleep=null):void;


    // unqueue
    // lance le processus de unqueue
    // possible de mettre une limite, une limite de temps et un temps de sleep entre chaque unqueue
    public static function triggerUnqueue(?int $limit=null,?int $timeLimit=null,?float $sleep=null):?array;
}
?>