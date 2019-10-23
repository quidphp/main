<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/core/blob/master/LICENSE
 */

namespace Quid\Main\File;
use Quid\Base;
use Quid\Main;

// js
// class for a js file
class Js extends Text
{
    // config
    public static $config = [
        'group'=>'js'
    ];
}

// init
Js::__init();
?>