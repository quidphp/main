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

// doc
// class for a doc file, like microsoft word
class Doc extends Text
{
    // config
    protected static array $config = [
        'group'=>'doc'
    ];
}

// init
Doc::__init();
?>