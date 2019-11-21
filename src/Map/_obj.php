<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 * Readme: https://github.com/quidphp/main/blob/master/README.md
 */

namespace Quid\Main\Map;

// _obj
// trait that grants methods to work with a collection containing instantiated objects
trait _obj
{
    // trait
    use _classeObj;


    // classeOrObj
    // retourne que le trait doit utilisé l'appelation d'objet
    final public static function classeOrObj():string
    {
        return 'obj';
    }
}
?>