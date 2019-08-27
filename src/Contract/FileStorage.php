<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main\Contract;

// fileStorage
interface FileStorage
{
	// storageDirname
	// retourne le dirname pour le storage
	public static function storageDirname():string;
}
?>