<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main;

// arrs
// class for a collection containing a multidimensional array
class Arrs extends Map
{
    // trait
    use Map\_arrs;


    // config
    protected static array $config = [];
}
?>