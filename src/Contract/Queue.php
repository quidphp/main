<?php
declare(strict_types=1);
namespace Quid\Main\Contract;
use Quid\Main;

// queue
interface Queue
{
	// queue
	// créer une nouvelle entrée dans la queue
	public static function queue(...$values):?Queue;
	
	
	// getQueued
	// retourne un objet avec toutes les entrées queued
	public static function getQueued(?int $limit=null):?Main\Map;
	
	
	// triggerUnqueueOnCloseDown
	// enregistre la méthode unqueue pour qu'elle s'éxécute au closeDown
	public static function triggerUnqueueOnCloseDown(?int $limit=null,?int $timeLimit=null,?float $sleep=null):void;
	
	
	// unqueue
	// lance le processus de unqueue
	// possible de mettre une limite, une limite de temps et un temps de sleep entre chaque unqueue
	public static function triggerUnqueue(?int $limit=null,?int $timeLimit=null,?float $sleep=null):?array;
}
?>