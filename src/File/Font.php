<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main\File;

// font
// class for a font file (like ttf)
class Font extends Binary
{
    // config
    public static $config = [
        'group'=>'font'
    ];
}

// init
Font::__init();
?>