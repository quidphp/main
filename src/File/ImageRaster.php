<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/core/blob/master/LICENSE
 */

namespace Quid\Main\File;
use Quid\Base;

// imageRaster
// class for a pixelated image file
class ImageRaster extends Image
{
    // config
    public static $config = [
        'group'=>'imageRaster'
    ];


    // captcha
    // écrit un captcha dans le fichier image
    public function captcha(string $value,?string $font=null,?array $option=null):self
    {
        Base\ImageRaster::captcha($value,$font,$this->resource(),$option);

        return $this;
    }
}

// init
ImageRaster::__init();
?>