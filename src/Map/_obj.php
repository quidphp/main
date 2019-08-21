<?php
declare(strict_types=1);
namespace Quid\Main\Map;
use Quid\Base;

// _obj
trait _obj
{
	// trait
	use _classeObj;
	
	
	// classeOrObj
	// retourne que le trait doit utilisé l'appelation d'objet
	public static function classeOrObj():string
	{
		return 'obj';
	}
}
?>