<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 * Readme: https://github.com/quidphp/main/blob/master/README.md
 */

namespace Quid\Main;

// arrObj
// abstract class that implements the methods necessary for the ArrayAccess, Countable and Iterator interfaces
abstract class ArrObj extends Root implements \ArrayAccess, \Countable, \Iterator
{
    // trait
    use _arrObj;


    // config
    public static array $config = [];
}
?>