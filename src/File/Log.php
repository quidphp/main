<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main\File;
use Quid\Main;

// log
// class for a log storage file
class Log extends Dump implements Main\Contract\Log, Main\Contract\FileStorage
{
    // trait
    use _log;


    // config
    protected static array $config = [
        'dirname'=>'[storageLog]',
        'deleteTrim'=>50
    ];
}

// init
Log::__init();
?>