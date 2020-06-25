<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main\File;
use Quid\Main;

// binary
// abstract class for a binary file
abstract class Binary extends Main\File
{
    // config
    protected static array $config = [];
}

// init
Binary::__init();
?>