<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main\File;
use Quid\Main;

// binary
// abstract class for a binary file
abstract class Binary extends Main\File
{
    // config
    public static $config = [];
}

// init
Binary::__init();
?>