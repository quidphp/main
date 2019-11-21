<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 * Readme: https://github.com/quidphp/main/blob/master/README.md
 */

namespace Quid\Main\Contract;
use Quid\Main;

// catchable
// interface to mark an exception as catchable
interface Catchable
{
    // catched
    // permet à l'exception de se déclencher en partir lors d'un catch
    public function catched(?array $option=null):Main\Error;
}
?>