<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main\File;

// font
// class for a font file (like ttf)
class Font extends Binary
{
    // config
    protected static array $config = [
        'group'=>'font'
    ];
}

// init
Font::__init();
?>