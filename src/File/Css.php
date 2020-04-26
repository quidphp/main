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

// css
// class for a css or scss file
class Css extends Text
{
    // trait
    use _concatenate;


    // config
    public static array $config = [
        'group'=>'css',
        'concatenateExtension'=>['css','scss']
    ];
}

// init
Css::__init();
?>