<?php
declare(strict_types=1);
namespace Quid\Main;

// _rootClone
trait _rootClone
{
	// trait
	use _root;
	
	
	// clone
	// ce qui se passe en cas de clone
	public function __clone()
	{
		return static::throw('notAllowed');
	}
}
?>