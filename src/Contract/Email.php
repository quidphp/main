<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/base/blob/master/LICENSE
 */

namespace Quid\Main\Contract;
use Quid\Main;

// email
interface Email
{
	// contentType
	// retourne le type de contenu du email
	public function contentType();


	// subject
	// retourne le sujet du email
	public function subject():string;


	// body
	// retourne le body du email
	public function body():string;


	// send
	// envoie le courriel maintenant
	public function send($key,$to,?array $replace=null,?array $message=null):bool;


	// sendOnCloseDown
	// envoie le courriel à la fermeture du script
	public function sendOnCloseDown($key,$to,?array $replace=null,?array $message=null):void;


	// queue
	// queue le courriel pour envoie plus tard
	public function queue($key,$to,?array $replace=null,?array $message=null):bool;


	// dispatch
	// dispatch le courriel selon la méthode par défaut défini dans la classe de courriel
	public function dispatch($key,$to,?array $replace=null,?array $message=null):bool;


	// getSegmentChars
	// retourne les caractères de segments à utiliser
	public static function getSegmentChars();


	// serviceMailer
	// retourne l'objet à utiliser pour envoyer le courriel
	public static function serviceMailer($key=null):Main\ServiceMailer;
}
?>