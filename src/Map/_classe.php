<?php
declare(strict_types=1);
namespace Quid\Main\Map;
use Quid\Base;

// _classe
trait _classe
{
	// trait
	use _classeObj;
	
	
	// classeOrObj
	// retourne que le trait doit utilisé l'appelation de classe
	public static function classeOrObj():string
	{
		return 'classe';
	}
}
?>