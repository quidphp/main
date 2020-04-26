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

// flash
// class for a collection containing flash-like data (delete on read)
class Flash extends Map
{
    // trait
    use Map\_flash;


    // config
    public static array $config = [];


    // dynamique
    protected ?array $mapAllow = ['set','unset','serialize','empty']; // méthodes permises
}
?>