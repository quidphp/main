<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main;
use Quid\Base;

// responseCurrent
// class for an object that acts as representation of current response
class ResponseCurrent extends ArrObj
{
    // trait
    use _inst;


    // config
    protected static array $config = [];


    // construct
    // cconstructeur privé, rien ne se passe, objet singleton
    final private function __construct() {}


    // call
    // renvoie vers base/response
    // envoie une exception si la méthode statique n'existe pas
    final public function __call(string $method,array $args):mixed
    {
        $return = null;

        if(Base\Response::classHasMethod($method))
        $return = Base\Response::$method(...$args);

        else
        static::throw('unknownMethod',$method);

        return $return;
    }


    // arr
    // retourne le tableau des en-têtes
    final protected function arr():array
    {
        return $this->headers();
    }



    // offsetExists
    // retourne vrai si le header existe
    public function offsetExists($key):bool
    {
        return $this->headerExists($key);
    }


    // offsetGet
    // retourne la valeur du header
    public function offsetGet($key):mixed
    {
        return ($this->offsetExists($key))? $this->getHeader($key):static::throw($key);
    }


    // offsetSet
    // ajoute ou change la valeur d'un header
    public function offsetSet($key,$value):void
    {
        $this->setHeader($key,$value);
    }


    // offsetUnset
    // enlève un header
    public function offsetUnset($key):void
    {
        ($this->offsetExists($key))? $this->unsetHeader($key):static::throw($key);
    }


    // singleton
    // crée ou retourne le singleton de l'objet réponse
    final public static function singleton():self
    {
        return static::instSafe() ?: static::instNew();
    }
}
?>