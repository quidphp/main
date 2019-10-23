<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
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
    public static $config = [
        'dirname'=>'[storageLog]',
        'deleteTrim'=>50
    ];
}

// init
Log::__init();
?>