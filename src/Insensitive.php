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