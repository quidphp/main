<?php
declare(strict_types=1);
namespace Quid\Main\Contract;

// fileStorage
interface FileStorage
{
	// storageDirname
	// retourne le dirname pour le storage
	public static function storageDirname():string;
}
?>