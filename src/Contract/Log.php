<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 * Readme: https://github.com/quidphp/main/blob/master/README.md
 */

namespace Quid\Main\Contract;

// log
// interface to detail the methods required for implementing logging functionality to an object
interface Log
{
    // log
    // crée une nouvelle entrée du log maintenant
    public static function log(...$values):?self;


    // logCloseDownCliNow
    // permet de logger maintenant si on est en cli, sinon envoie en closedown
    public static function logCloseDownCliNow(...$values):void;


    // logCloseDown
    // queue la création d'une nouvelle entrée du log au closeDown
    public static function logCloseDown(...$values):void;


    // logTrim
    // trim le nombre de log par une valeur paramétré
    public static function logTrim():?int;
}
?>