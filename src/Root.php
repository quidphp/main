<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main;
use Quid\Base;

// root
// abstract class extended by all others
abstract class Root extends Base\Root implements \JsonSerializable
{
    // trait
    use _rootClone;


    // config
    protected static array $config = [];
}
?>