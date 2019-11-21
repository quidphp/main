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

// _cloner
// trait that provides a method to make sure the collection is cloned on every update
trait _cloner
{
    // onPrepareThis
    // l'objet est cloner avant chaque modification
    final protected function onPrepareThis(string $method):Main\Map
    {
        return clone $this;
    }
}
?>