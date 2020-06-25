<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
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


    // filterKeep
    // garde seulement les clés données en argument
    final public function filterKeep(...$values)
    {
        $values = $this->prepareKeys(...$values);
        return $this->filter(fn($value,$key) => in_array($key,$values,true));
    }


    // filterReject
    // garde seulement les clés non données en argument
    final public function filterReject(...$values)
    {
        $values = $this->prepareKeys(...$values);
        return $this->filter(fn($value,$key) => !in_array($key,$values,true));
    }
}
?>