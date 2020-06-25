<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main;

// arrObj
// abstract class that implements the methods necessary for the ArrayAccess, Countable and Iterator interfaces
abstract class ArrObj extends Root implements \ArrayAccess, \Countable, \Iterator
{
    // trait
    use _arrObj;


    // config
    protected static array $config = [];
}
?>