<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main;
use Quid\Base;

// exception
// class for a default exception
class Exception extends \Exception implements \Serializable, \JsonSerializable
{
    // trait
    use _root;
    use Base\_root;


    // config
    public static $config = [
        'code'=>31, // code de l'exception
        'cleanBuffer'=>false,
        'output'=>false,
        'kill'=>false
    ];


    // dynamique
    protected $args = null; // retourne l'argument initial message du constructeur


    // construct
    // créer un nouvel objet exception sans le lancer
    // le code est déterminé dans la classe exception
    // message est passé dans base\exception message
    public function __construct($message='',?\Throwable $previous=null,?array $attr=null,...$args)
    {
        $this->makeAttr($attr);
        $this->setArgs(...$args);
        parent::__construct(Base\Exception::message($message),$this->getAttr('code'),$previous);

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
    protected function setArgs(...$values):void
    {
        $this->args = $values;

        return;
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
    public function error(?array $attr=null):Error
    {
        return Error::newOverload($this,null,$attr);
    }


    // trigger
    // envoie à la classse error et trigge l'erreur
    public function trigger(?array $attr=null):Error
    {
        $class = Error::getOverloadClass();
        $return = $class::exception($this,$attr);

        return $return;
    }


    // echoOutput
    // affiche le output de l'erreur de l'exception
    public function echoOutput():void
    {
        $this->error()->output();

        return;
    }


    // getOutput
    // envoie à la classe error, génère le output et retourne la string
    // ne crée pas d'entrée dans le log
    public function getOutput():string
    {
        return $this->error()->getOutput();
    }


    // log
    // envoie à la classe error et log l'exception selon les classes paramétrés dans error
    public function log(?array $attr=null):self
    {
        $this->error($attr)->log();

        return $this;
    }


    // com
    // envoie à la classe error et met l'error dans com
    public function com(?array $attr=null):self
    {
        $this->error($attr)->com();

        return $this;
    }


    // onCatched
    // envoie à la classse error et trigge l'erreur
    // utilise les config catched (donc devrait générer une erreur silencieuse)
    public function onCatched(?array $attr=null):Error
    {
        return static::staticCatched($this,$attr);
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
    public static function staticCatched(\Exception $exception,?array $attr=null):Error
    {
        $exceptionOption = ($exception instanceof self)? $exception->attr():null;
        $attr = Base\Arr::replace(static::$config,$exceptionOption,$attr);
        $class = Error::getOverloadClass();
        $return = $class::exception($exception,$attr);

        return $return;
    }
}
?>