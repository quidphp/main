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
use Quid\Base;

// _reference
// trait that permits the collection to work from a reference array source
trait _reference
{
    // construct
    // construit l'objet et attribue la référence
    final public function __construct(array &$value)
    {
        $this->data =& $value;

        return;
    }


    // onCheckArr
    // s'il y a is, fait une validation sur l'ensemble car l'original peut avoir changé
    final protected function onCheckArr():void
    {
        if(!empty(static::$is) && !Base\Arr::validate(static::$is,$this->data))
        static::throw('onlyAccepts',static::$is);

        return;
    }
}
?>