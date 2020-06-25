<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main\File;

// css
// class for a css or scss file
class Css extends Text
{
    // trait
    use _concatenate;


    // config
    protected static array $config = [
        'group'=>'css',
        'concatenateExtension'=>['css','scss']
    ];
}

// init
Css::__init();
?>