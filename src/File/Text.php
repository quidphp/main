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