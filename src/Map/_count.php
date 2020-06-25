<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main\Map;
use Quid\Base;
use Quid\Main;

// _count
// trait that provides a method to limit the length of a collection
trait _count
{
    // unsetAfterCount
    // enlève les entrées après un certain nombre
    final public function unsetAfterCount(int $count):Main\Map
    {
        $this->checkAllowed('unsetAfterCount');
        $return = $this->onPrepareThis('unsetAfterCount');
        $data =& $return->arr();
        $data = Base\Arr::unsetAfterCount($count,$data);

        if(empty($this->mapAfter['unsetAfterCount']))
        $return->checkAfter();

        return $return;
    }
}
?>