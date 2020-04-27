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