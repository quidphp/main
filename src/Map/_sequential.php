<?php
declare(strict_types=1);
namespace Quid\Main\Map;
use Quid\Main;

// _sequential
trait _sequential
{
	// sequential
	// ramène les clés de la map séquentielle, numérique et en ordre
	public function sequential():Main\Map
	{
		$this->checkAllowed('sequential');
		$return = $this->onPrepareThis('sequential');
		$data =& $return->arr();
		$data = array_values($data);
		
		return $return;
	}
}
?>