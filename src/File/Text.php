<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/core/blob/master/LICENSE
 */

namespace Quid\Main\File;
use Quid\Main;

// text
// abstract class for a text file
abstract class Text extends Main\File
{
    // config
    public static $config = [
        'group'=>'text'
    ];
}

// init
Text::__init();
?>