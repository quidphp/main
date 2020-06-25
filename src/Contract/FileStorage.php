<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main\Contract;

// fileStorage
// interface to describe a file objet which has a defined storage folder
interface FileStorage
{
    // storageDirname
    // retourne le dirname pour le storage
    public static function storageDirname():string;
}
?>