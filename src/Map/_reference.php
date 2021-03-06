<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
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
    }


    // onCheckArr
    // s'il y a is, fait une validation sur l'ensemble car l'original peut avoir changé
    final protected function onCheckArr():void
    {
        $is = $this->mapIs;

        if(!empty($is) && !Base\Arr::validate($is,$this->data))
        static::throw('onlyAccepts',static::$is);
    }
}
?>