<?php
declare(strict_types=1);
namespace Quid\Main;

// catchableException
class CatchableException extends Exception implements Contract\Catchable
{
	// config
	public static $config = array(
		'code'=>32, // code de l'exception
		'option'=>array(
			'com'=>true)
	);
}

// config
CatchableException::__config();
?>