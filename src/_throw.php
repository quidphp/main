<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main;
use Quid\Base;

// _throw
// trait that provides static methods to throw exception from an object
trait _throw
{
    // typecheck
    // lance une exception si la variable n'est pas compatible au type donnée en deuxième argument
    // retourne la variable
    protected static function typecheck($return,$type,...$args)
    {
        $throw = false;
        $args = $args ?: [$return];

        if($type === true && empty($type))
        $throw = true;

        elseif(is_string($type) && !$return instanceof $type)
        $throw = true;

        if($throw === true)
        static::throw('expected',$type,...$args);

        return $return;
    }


    // throw
    // lance une nouvelle exception
    // ajoute la classe et méthode statique appelant au début du message de l'exception
    final protected static function throw(...$values):void
    {
        $class = Exception::classOverload();
        static::throwCommon($class,$values);
    }


    // catchable
    // lance une exception attrapable
    // ajoute la classe et méthode statique appelant au début du message de l'exception
    // la différence avec throw est qu'il y a maintenant un tableau action en premier argument, qui permet de définir le traitement lors du catched
    final protected static function catchable(?array $option=null,...$values):void
    {
        $class = CatchableException::classOverload();
        static::throwCommon($class,$values,$option);
    }


    // throwCommon
    // méthode commune utilisé pour envoyer des exceptions
    final protected static function throwCommon(string $class,array $values,?array $option=null):void
    {
        $source = (static::class !== self::class)? static::class:null;
        $message = Base\Exception::classFunction(Base\Debug::traceIndex(3),$source,$values);
        throw new $class($message,null,$option,...$values);
    }
}
?>