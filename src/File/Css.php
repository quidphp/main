<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/core/blob/master/LICENSE
 */

namespace Quid\Main\File;
use Quid\Base;
use Quid\Main;

// css
// class for a css or scss file
class Css extends Text
{
    // config
    public static $config = [
        'group'=>'css'
    ];
}

// init
Css::__init();
?>