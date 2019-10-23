<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main\File;

// php
// class for a php file
class Php extends Text
{
    // config
    public static $config = [
        'group'=>'php'
    ];
}

// init
Php::__init();
?>