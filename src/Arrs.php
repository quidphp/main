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

// arrs
// class for a collection containing a multidimensional array
class Arrs extends Map
{
    // trait
    use Map\_arrs;


    // config
    public static array $config = [];
}
?>