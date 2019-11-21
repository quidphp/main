<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 * Readme: https://github.com/quidphp/main/blob/master/README.md
 */

namespace Quid\Main\File;

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