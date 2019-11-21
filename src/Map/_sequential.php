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

// _sequential
// trait that makes sure the keys of the collection are always sequential
trait _sequential
{
    // sequential
    // ramène les clés de la map séquentielle, numérique et en ordre
    final public function sequential():Main\Map
    {
        $this->checkAllowed('sequential');
        $return = $this->onPrepareThis('sequential');
        $data =& $return->arr();
        $data = array_values($data);

        return $return;
    }
}
?>