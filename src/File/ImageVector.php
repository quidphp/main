<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main\File;

// imageVector
// class for a vector image file (like svg)
class ImageVector extends Image
{
    // config
    protected static array $config = [
        'group'=>'imageVector'
    ];
}

// init
ImageVector::__init();
?>