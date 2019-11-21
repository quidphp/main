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