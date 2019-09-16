<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main\Map;
use Quid\Base;
use Quid\Main;

// _insensitive
// trait that transforms the collection from case sensitive to insensitive
trait _insensitive
{
    // append
    // version insensible de append
    public function append(...$values):Main\Map
    {
        $this->checkAllowed('append');
        $return = $this->onPrepareThis('append');
        $values = $return->prepareReplaces(...$values);
        $return->checkBefore(true,...$values);

        $data =& $return->arr();
        $data = Base\Arr::iappend($data,...$values);

        return $return->checkAfter();
    }


    // prepend
    // version insensible de prepend
    public function prepend(...$values):Main\Map
    {
        $this->checkAllowed('prepend');
        $return = $this->onPrepareThis('prepend');
        $values = $return->prepareReplaces(...$values);
        $return->checkBefore(true,...$values);

        $data =& $return->arr();
        $data = Base\Arr::iprepend($data,...$values);

        return $return->checkAfter();
    }


    // isSensitive
    // retourne faux
    public static function isSensitive():bool
    {
        return false;
    }
}
?>