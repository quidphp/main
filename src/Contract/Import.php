<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/base/blob/master/LICENSE
 */

namespace Quid\Main\Contract;

// import
interface Import
{
	// sourceRewind
	// ramène le pointeur de la source au début
	public function sourceRewind():void;


	// sourceOne
	// retourne une entrée de la source
	// i agit comme référence
	public function sourceOne($offset=true,$length=true,int &$i,?array $option=null);


	// targetInsert
	// fait une insertion sur la target
	public function targetInsert(array $data,?array $option=null):bool;


	// targetUpdate
	// fait une mise à jour sur la target
	public function targetUpdate(array $data,int $int,?array $option=null):bool;


	// targetDelete
	// fait une suppresion sur la target
	public function targetDelete(int $int,?array $option=null):bool;


	// targetTruncate
	// vide la target
	public function targetTruncate(?array $option=null):bool;
}
?>