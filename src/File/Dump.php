<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main\File;
use Quid\Base;

// dump
// class for file which contains an exported value (similar to var_export)
class Dump extends Html
{
    // config
    protected static array $config = [
        'write'=>[
            'callback'=>[Base\Debug::class,'varGet']]
    ];
}

// init
Dump::__init();
?>