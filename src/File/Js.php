<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main\File;

// js
// class for a js file
class Js extends Text
{
    // trait
    use _concatenate;


    // config
    protected static array $config = [
        'group'=>'js',
        'concatenateExtension'=>['js','jsx']
    ];
}

// init
Js::__init();
?>