<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main\File;
use Quid\Base\Html;

// image
// abstract class for an image file (raster or vector)
abstract class Image extends Binary
{
    // config
    protected static array $config = [];


    // img
    // génère un tag img à partir du fichier image
    // note si l'image n'a pas un chemin accessible via http, la resource sera affiché sous forme de base64
    final public function img($alt=null,$attr=null,bool $absolute=false):?string
    {
        $return = null;
        $src = $this->pathToUri($absolute) ?? $this->resource();
        $alt ??= $this->getAttr('defaultAlt');
        $return = Html::img($src,$alt,$attr);

        return $return;
    }
}

// init
Image::__init();
?>