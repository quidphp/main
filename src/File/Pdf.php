<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/core/blob/master/LICENSE
 */

namespace Quid\Main\File;

// pdf
// class for pdf file
class Pdf extends Binary
{
    // config
    public static $config = [
        'group'=>'pdf'
    ];
}

// init
Pdf::__init();
?>