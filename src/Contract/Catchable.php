<?php
declare(strict_types=1);
namespace Quid\Main\Contract;
use Quid\Main;

// catchable
interface Catchable
{
	// onCatched
	// permet à l'exception de se déclencher en partir lors d'un catch
	public function onCatched(?array $option=null):Main\Error;
}
?>