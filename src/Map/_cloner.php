<?php
declare(strict_types=1);
namespace Quid\Main\Map;
use Quid\Main;

// _cloner
trait _cloner
{
	// onPrepareThis
	// l'objet est cloner avant chaque modification
	protected function onPrepareThis(string $method):Main\Map 
	{
		return clone $this;
	}
}
?>