<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main;

// std
// class for a collection with a complete set of methods
class Std extends Map
{
    // trait
    use Map\_arr;
    use Map\_basic;
    use Map\_count;
    use Map\_readOnly;
    use Map\_sort;
    use Map\_sequential;
    use Map\_filter;
    use Map\_map;


    // config
    protected static array $config = [];
}
?>