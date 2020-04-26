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
use Quid\Main;

// _filter
// trait that provides a method to filter a collection by providing a condition or callback
trait _filter
{
    // filter
    // permet de filtrer l'objet à partir d'une condition à ce moment seul les entrées true sont gardés
    final public function filter(\Closure $closure):Main\Map
    {
        $this->checkAllowed('filter');
        $return = $this->onPrepareThis('filter');
        $data =& $return->arr();

        foreach ($return->arr() as $key => $value)
        {
            if(!$closure($value,$key))
            unset($data[$key]);
        }

        return $return->checkAfter();
    }
}
?>