<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/base/blob/master/LICENSE
 */

namespace Quid\Main;
use Quid\Base;

// exception
class Exception extends \Exception implements \Serializable, \JsonSerializable
{
	// trait
	use _root;
	use _option;
	use Base\_root;
	
	
	// config
	public static $config = [
		'code'=>31, // code de l'exception
		'option'=>[ // option pour error lors de onCatched
			'cleanBuffer'=>false,
			'html'=>false,
			'kill'=>false]
	];


	// dynamique
	protected $args = null; // retourne l'argument initial message du constructeur


	// construct
	// créer un nouvel objet exception sans le lancer
	// le code est déterminé dans la classe exception
	// message est passé dans base\exception message
	public function __construct($message='',?\Throwable $previous=null,?array $option=null,...$args)
	{
		$this->option($option);
		$this->setArgs(...$args);
		parent::__construct(Base\Exception::message($message),static::$config['code'],$previous);

		return;
	}


	// invoke
	// appel de l'exception, renvoie vers trigger
	public function __invoke(...$args)
	{
		return $this->trigger(...$args);
	}


	// toString
	// retourne le output de l'exception
	public function __toString():string
	{
		return static::output($this);
	}


	// cast
	// retourne le message de l'exception
	public function _cast():string
	{
		return $this->getMessage();
	}


	// setArgs
	// conserve l'argument message dans le constructeur de l'exception
	// méthode protégé
	protected function setArgs(...$values):self
	{
		$this->args = $values;

		return $this;
	}


	// args
	// retourne l'argument message dans le constructeur de l'exception
	public function args():array
	{
		return $this->args;
	}


	// messageArgs
	// retourne le message mais en utilisant les arguments du constructeur de l'exception
	// retourne un tableau compatible avec l'objet lang
	public function messageArgs():?array
	{
		$return = null;
		$args = $this->args();

		if(!empty($args))
		{
			$key = Base\Arr::valueFirst($args);

			if(is_string($key) && !empty($key))
			{
				$return = [];
				$return['key'][] = 'exception';
				$return['key'][] = static::className(true);
				$return['key'][] = $key;
				$args = Base\Arr::spliceFirst($args);
				$return['replace'] = $args;
			}
		}

		return $return;
	}


	// getMessageArgs
	// retourne le message passé dans lang si objet lang est fourni et s'il y a message args
	// sinon retourne le message normal
	public function getMessageArgs(?Lang $lang=null,bool $default=true):string
	{
		$return = '';

		if(!empty($lang))
		{
			$messageArgs = $this->messageArgs();

			if(!empty($messageArgs))
			{
				$safe = $lang->safe(...array_values($messageArgs));
				if(is_string($safe))
				$return = $safe;
			}
		}

		if(empty($return) && $default === true)
		$return = $this->getMessage();

		return $return;
	}


	// content
	// retourne le contenu s'il y en a
	public function content():?string
	{
		return null;
	}


	// error
	// envoie à la classe error
	public function error(?array $option=null):Error
	{
		return Error::newOverload($this,null,$option);
	}


	// trigger
	// envoie à la classse error et trigge l'erreur
	public function trigger(?array $option=null):Error
	{
		$class = Error::getOverloadClass();
		$return = $class::exception($this,$option);

		return $return;
	}


	// html
	// envoie à la classe error, génère l'html et retourne la string
	// ne crée pas d'entrée dans le log
	public function html():string
	{
		return $this->error()->html();
	}


	// log
	// envoie à la classe error et log l'exception selon les classes paramétrés dans error
	public function log(?array $option=null):self
	{
		$this->error($option)->log();

		return $this;
	}


	// com
	// envoie à la classe error et met l'error dans com
	public function com(?array $option=null):self
	{
		$this->error($option)->com();

		return $this;
	}


	// onCatched
	// envoie à la classse error et trigge l'erreur
	// utilise les config catched (donc devrait générer une erreur silencieuse)
	public function onCatched(?array $option=null):Error
	{
		return static::staticCatched($this,$option);
	}


	// throw
	// lance une nouvelle exception
	// ajoute la classe et méthode statique appelant au début du message de l'exception
	public static function throw(...$values):void
	{
		throw new static(Base\Exception::classFunction(Base\Debug::traceIndex(2),null,$values));

		return;
	}


	// stack
	// retourne les parents d'une throwable
	public static function stack(\Throwable $throwable,bool $reverse=false):array
	{
		$return = [];

		while($throwable = $throwable->getPrevious())
		{
			$return[] = $throwable;
		}

		if($reverse === true)
		$return = array_reverse($return);

		return $return;
	}


	// output
	// permet de générer un output rapide à partir d'une throwable
	// utiliser pour le stack trace dans core/error
	public static function output(\Throwable $throwable):string
	{
		$return = get_class($throwable);
		$return .= ' (#'.$throwable->getCode().') -> ';
		$return .= $throwable->getMessage();
		$return .= ' -> ';
		$return .= $throwable->getFile();
		$return .= '::';
		$return .= $throwable->getLine();

		return $return;
	}


	// staticCatched
	// permet d'attraper une exception non quid et de lui faire le traitement onCatched
	public static function staticCatched(\Exception $exception,?array $option=null):Error
	{
		$exceptionOption = ($exception instanceof self)? $exception->option():null;
		$option = Base\Arr::replace(static::$config['option'],$exceptionOption,$option);
		$class = Error::getOverloadClass();
		$return = $class::exception($exception,$option);

		return $return;
	}
}
?>