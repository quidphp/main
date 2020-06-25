<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main\File;

// html
// class for an html file
class Html extends Text
{
    // config
    protected static array $config = [
        'group'=>'html'
    ];
}

// init
Html::__init();
?>