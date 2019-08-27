<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

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