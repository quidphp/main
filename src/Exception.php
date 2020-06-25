<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 * Readme: https://github.com/quidphp/main/blob/master/README.md
 */

namespace Quid\Main;
use Quid\Base;

// exception
// class for a default exception
class Exception extends \Exception implements \JsonSerializable
{
    // trait
    use _root;
    use Base\_root;


    // config
    protected static array $config = [
        'code'=>31, // code de l'exception
        'cleanBuffer'=>false,
        'output'=>false,
        'kill'=>false
    ];


    // dynamique
    protected array $args; // retourne l'argument initial message du constructeur


    // construct
    // créer un nouvel objet exception sans le lancer
    // le code est déterminé dans la classe exception
    // message est passé dans base\exception message
    final public function __construct($message='',?\Throwable $previous=null,?array $attr=null,...$args)
    {
        $this->makeAttr($attr);
        $this->setArgs(...$args);
        parent::__construct(Base\Exception::message($message),$this->getAttr('code'),$previous);
    }


    // invoke
    // appel de l'exception, renvoie vers trigger
    final public function __invoke(...$args)
    {
        return $this->trigger(...$args);
    }


    // toString
    // retourne le output de l'exception
    final public function __toString():string
    {
        return static::output($this);
    }


    // cast
    // retourne le message de l'exception
    final public function _cast():string
    {
        return $this->getMessage();
    }


    // setArgs
    // conserve l'argument message dans le constructeur de l'exception
    final protected function setArgs(...$values):void
    {
        $this->args = $values;
    }


    // args
    // retourne l'argument message dans le constructeur de l'exception
    final public function args():array
    {
        return $this->args;
    }


    // messageArgs
    // retourne le message mais en utilisant les arguments du constructeur de l'exception
    // retourne un tableau compatible avec l'objet lang
    final public function messageArgs():?array
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
    final public function getMessageArgs(?Lang $lang=null,bool $default=true):string
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
    final public function error(?array $attr=null):Error
    {
        return Error::newOverload($this,null,$attr);
    }


    // trigger
    // envoie à la classse error et trigge l'erreur
    final public function trigger(?array $attr=null):Error
    {
        $class = Error::classOverload();
        $return = $class::exception($this,$attr);

        return $return;
    }


    // echoOutput
    // affiche le output de l'erreur de l'exception
    final public function echoOutput():void
    {
        $this->error()->output();
    }


    // getOutput
    // envoie à la classe error, génère le output et retourne la string
    // ne crée pas d'entrée dans le log
    final public function getOutput():string
    {
        return $this->error()->getOutput();
    }


    // log
    // envoie à la classe error et log l'exception selon les classes paramétrés dans error
    final public function log(?array $attr=null):self
    {
        $this->error($attr)->log();

        return $this;
    }


    // com
    // envoie à la classe error et met l'error dans com
    final public function com(?array $attr=null):self
    {
        $this->error($attr)->com();

        return $this;
    }


    // catched
    // envoie à la classse error et trigge l'erreur
    // utilise les config catched (donc devrait générer une erreur silencieuse)
    final public function catched($attr=null):Error
    {
        return static::staticCatched($this,$attr);
    }


    // throw
    // lance une nouvelle exception
    // ajoute la classe et méthode statique appelant au début du message de l'exception
    final public static function throw(...$values):void
    {
        throw new static(Base\Exception::classFunction(Base\Debug::traceIndex(2),null,$values));
    }


    // stack
    // retourne les parents d'une throwable
    final public static function stack(\Throwable $throwable,bool $reverse=false):array
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
    final public static function output(\Throwable $throwable):string
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
    // permet d'attraper une throwable non quid et de lui faire le traitement catched
    // si attr est array, alors merge par dessus un tableau d'option ou tout est à false
    // si null utilise seulement les attributs de l'exception
    final public static function staticCatched(\Throwable $throwable,?array $attr=null):Error
    {
        if(is_array($attr))
        $attr = Base\Arr::replace(['output'=>false,'kill'=>false,'com'=>false,'log'=>false,'cleanBuffer'=>false],$attr);

        $throwableOption = ($throwable instanceof self)? $throwable->attr():null;
        $attr = Base\Arr::replace(static::$config,$throwableOption,$attr);
        $class = Error::classOverload();
        $return = $class::exception($throwable,$attr);

        return $return;
    }


    // staticToArray
    // retourne un tableau à partir d'une throwable
    final public static function staticToArray(\Throwable $throwable):array
    {
        $error = static::staticCatched($throwable,[]);

        return [
            'message'=>$error->getMessage(),
            'file'=>$error->getFile(),
            'line'=>$error->getLine(),
        ];
    }
}
?>