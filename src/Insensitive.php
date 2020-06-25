<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main;

// insensitive
// class for a collection containing an insensitive array
class Insensitive extends Map
{
    // trait
    use Map\_insensitive;


    // config
    protected static array $config = [];
}
?>