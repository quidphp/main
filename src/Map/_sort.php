<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main\Map;
use Quid\Main;
use Quid\Base;

// _sort
// trait that provides methods to change the order of entries within the collection
trait _sort
{
    // sort
    // sort les clés de la map
    public function sort($sort=true,int $type=SORT_FLAG_CASE | SORT_NATURAL):Main\Map
    {
        $this->checkAllowed('sort');
        $return = $this->onPrepareThis('sort');
        $data =& $return->arr();
        $data = Base\Arr::keysSort($data,$sort,$type);

        return $return;
    }


    // shuffle
    // shuffle les valeurs de la map tout en conservant les clés
    public function shuffle():Main\Map
    {
        $this->checkAllowed('sort');
        $return = $this->onPrepareThis('sort');
        $data =& $return->arr();
        $data = Base\Arr::shuffle($data,true);

        return $return;
    }


    // reverse
    // reverse l'ordre des valeurs de la map tout en conservant les clés
    public function reverse(bool $preserve=true):Main\Map
    {
        $this->checkAllowed('sort');
        $return = $this->onPrepareThis('sort');
        $data =& $return->arr();
        $data = Base\Arr::reverse($data,true);

        return $return;
    }
}
?>