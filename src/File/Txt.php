<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main\File;

// txt
// class for txt file (like txt)
class Txt extends Text
{
    // config
    protected static array $config = [];
}

// init
Txt::__init();
?>