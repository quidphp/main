<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main\File;
use Quid\Base;

// imageRaster
// class for a pixelated image file
class ImageRaster extends Image
{
    // config
    protected static array $config = [
        'group'=>'imageRaster'
    ];


    // captcha
    // écrit un captcha dans le fichier image
    final public function captcha(string $value,?string $font=null,?array $option=null):self
    {
        Base\ImageRaster::captcha($value,$font,$this->resource(),$option);

        return $this;
    }
}

// init
ImageRaster::__init();
?>