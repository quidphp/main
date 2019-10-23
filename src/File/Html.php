<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/core/blob/master/LICENSE
 */

namespace Quid\Main\File;

// html
// class for an html file
class Html extends Text
{
    // config
    public static $config = [
        'group'=>'html'
    ];
}

// init
Html::__init();
?>