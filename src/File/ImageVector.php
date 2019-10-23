<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main\File;

// imageVector
// class for a vector image file (like svg)
class ImageVector extends Image
{
    // config
    public static $config = [
        'group'=>'imageVector'
    ];
}

// init
ImageVector::__init();
?>