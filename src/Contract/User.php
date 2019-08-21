<?php
declare(strict_types=1);
namespace Quid\Main\Contract;
use Quid\Main;

// user
interface User
{
	// uid
	// retourne le uid du user
	public function uid():int;
	
	
	// role
	// retourne le role de la row user
	public function role():Main\Role;
}
?>