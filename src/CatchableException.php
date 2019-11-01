<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main;

// catchableException
// class for a catchable exception
class CatchableException extends Exception implements Contract\Catchable
{
    // config
    public static $config = [
        'code'=>32, // code de l'exception
        'com'=>true
    ];
}

// init
CatchableException::__init();
?>