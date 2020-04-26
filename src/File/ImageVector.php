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

// imageVector
// class for a vector image file (like svg)
class ImageVector extends Image
{
    // config
    public static array $config = [
        'group'=>'imageVector'
    ];
}

// init
ImageVector::__init();
?>