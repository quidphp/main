<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main\File;

// doc
// class for a doc file, like microsoft word
class Doc extends Text
{
    // config
    public static $config = [
        'group'=>'doc'
    ];
}

// init
Doc::__init();
?>