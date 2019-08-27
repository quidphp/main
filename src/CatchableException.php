<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/base/blob/master/LICENSE
 */

namespace Quid\Main;

// catchableException
class CatchableException extends Exception implements Contract\Catchable
{
	// config
	public static $config = [
		'code'=>32, // code de l'exception
		'option'=>[
			'com'=>true]
	];
}

// config
CatchableException::__config();
?>