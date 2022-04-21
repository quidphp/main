<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main\File;
use Quid\Base;

// image
// abstract class for an image file (raster or vector)
abstract class Image extends Binary
{
    // config
    protected static array $config = [];


    // img
    // génère un tag img à partir du fichier image
    // note si l'image n'a pas un chemin accessible via http, la resource sera affiché sous forme de base64
    final public function img($alt=null,$attr=null,?bool $absolute=null,bool $base64=false):?string
    {
        $return = null;
        $src = $this->pathToUri($absolute) ?? $this->resource();
        $alt ??= $this->getAttr('defaultAlt');
        $option = [];

        if($base64 === true)
        $option['base64'] = true;

        $return = Base\Html::img($src,$alt,$attr,$option); // garde base/html, fait une bogue dans preload

        return $return;
    }


    // img64
    // génère un tag img à partir du fichier image (en utilisant l'encodage base64)
    final public function img64($alt=null,$attr=null,bool $absolute=false)
    {
        return $this->img($alt,$attr,$absolute,true);
    }
}

// init
Image::__init();
?>