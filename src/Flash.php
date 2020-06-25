<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main;

// flash
// class for a collection containing flash-like data (delete on read)
class Flash extends Map
{
    // trait
    use Map\_flash;


    // config
    protected static array $config = [];


    // dynamique
    protected ?array $mapAllow = ['set','unset','serialize','empty']; // méthodes permises
}
?>