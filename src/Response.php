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

// response
// class with methods to manage an HTTP response
class Response extends Res
{
    // config
    protected static array $config = [];


    // dynamique
    protected ?array $mapAllow = ['clone']; // méthodes permises
    protected string $id; // id unique de la réponse
    protected Request $request; // instance de l'objet request
    protected ?array $header = null; // headers de la réponse
    protected ?int $timestamp = null; // timestamp de réception la réponse


    // construct
    // construit l'objet réponse à partir d'une requête
    // trigge la requête à la construction
    final public function __construct($request=null,?array $option=null)
    {
        $this->setRequest($request);
        $this->setId();
        $this->trigger($option);
    }


    // toString
    // retourne le body de la réponse
    final public function __toString():string
    {
        return $this->body();
    }


    // clone
    // clone est permis
    final public function __clone()
    {
        return;
    }


    // toArray
    // retourne tout l'objet sous forme de tableau sauf cache et index
    final public function toArray():array
    {
        return Base\Arr::unsets(['cache','index'],get_object_vars($this));
    }


    // cast
    // retourne le body de la réponse
    final public function _cast():string
    {
        return $this->body();
    }


    // setRequest
    // change l'objet requête de l'objet
    final protected function setRequest($request=null):void
    {
        if(!$request instanceof Request)
        $request = Request::newOverload($request);

        $this->request = $request;
    }


    // request
    // retourne l'objet requête
    final public function request():Request
    {
        return $this->request;
    }


    // trigger
    // trigge la requête et envoie le tableau curlExec dans la méthode setExec
    final protected function trigger(?array $option=null):void
    {
        $request = $this->request();
        $exec = $request->curlExec($option);
        $this->setExec($exec);
    }


    // setExec
    // traite un tableau exec, en provenance de request curlExec
    final protected function setExec(array $exec):void
    {
        if(Base\Arr::keysExists(['header','resource','timestamp'],$exec))
        {
            $this->setHeaders($exec['header']);
            $this->setResource($exec['resource']);
            $this->setTimestamp($exec['timestamp']);
        }

        else
        static::throw('invalidExecArray');
    }


    // setHeaders
    // attribue les headers de la réponse
    final protected function setHeaders(array $headers):void
    {
        $this->header = $headers;
    }


    // headers
    // retourne les headers de l'objet réponse
    final public function headers():array
    {
        return $this->header;
    }


    // setResource
    // change la resource phpTemp de l'objet réponse
    final public function setResource($value,?array $option=null):void
    {
        if(!empty($this->resource))
        static::throw('resourceAlreadySet');

        if(Base\Res::isPhpTemp($value))
        $this->resource = $value;

        else
        static::throw('invalidResource');
    }


    // setTimestamp
    // change le timestamp de réception de la réponse
    final protected function setTimestamp(int $value):void
    {
        $this->timestamp = $value;
    }


    // timestamp
    // retourne le timestamp de réception de la réponse, si existant
    final public function timestamp():?int
    {
        return $this->timestamp;
    }


    // setId
    // change le id unique de la réponse
    // génère un id unique en utilisant la config id length
    final protected function setId():void
    {
        $this->id = Base\Str::random(Base\Response::getConfig('idLength'));
    }


    // id
    // retourne le id unique de la réponse
    final public function id():string
    {
        return $this->id;
    }


    // isWritable
    // ne pas permettre écriture sur la resource de la réponse
    final public function isWritable():bool
    {
        return false;
    }


    // body
    // retourne le body de la réponse sous forme de string
    // si format est true, le body est formatté avant d'être retourné, support pour json
    final public function body(bool $format=false)
    {
        $return = $this->read();

        if($format === true)
        {
            if($this->isJson())
            $return = Base\Json::decode($return);
        }

        return $return;
    }


    // is200
    // retourne vrai si le code de la réponse est 200
    final public function is200():bool
    {
        return Base\Header::is200($this->headers());
    }


    // isCodePositive
    // retourne vrai si le code de la réponse est positive (200 à 399)
    final public function isCodePositive():bool
    {
        return Base\Header::isCodePositive($this->headers());
    }


    // isCodeLoggable
    // retourne vrai si le code de la réponse positive mais pas 301
    final public function isCodeLoggable():bool
    {
        return Base\Header::isCodeLoggable($this->headers());
    }


    // isCodeError
    // retourne vrai si le code de la réponse est 400 ou 404
    final public function isCodeError():bool
    {
        return Base\Header::isCodeError($this->headers());
    }


    // isCodeServerError
    // retourne vrai si le code de la réponse est 500
    final public function isCodeServerError():bool
    {
        return Base\Header::isCodeServerError($this->headers());
    }


    // isHtml
    // retourne vrai si la réponse est de content type html
    final public function isHtml():bool
    {
        return $this->isContentType('html');
    }


    // isJson
    // retourne vrai si la réponse est de content type json
    final public function isJson():bool
    {
        return $this->isContentType('json');
    }


    // isXml
    // retourne vrai si la réponse est de content type xml
    final public function isXml():bool
    {
        return $this->isContentType('xml');
    }


    // isCode
    // retourne vrai le code de la réponse est un de ceux donnés
    final public function isCode(...$values):bool
    {
        return Base\Header::isCode($values,$this->headers());
    }


    // isCodeBetween
    // retourne vrai si le code est entre les valeurs from et to
    final public function isCodeBetween($from,$to):bool
    {
        return Base\Header::isCodeBetween($from,$to,$this->headers());
    }


    // isCodeIn
    // retourne vrai si le code se trouve dans le groupe (la centaine) donné en argument
    final public function isCodeIn($value):bool
    {
        return Base\Header::isCodeIn($value,$this->headers());
    }


    // isContentType
    // retourne vrai si le tableau header contient le content-type donné en argument
    // si la réponse est false, essaie via mimeGroup
    final public function isContentType($value):bool
    {
        $return = Base\Header::isContentType($value,$this->headers());

        if($return === false)
        $return = $this->isMimeGroup($value);

        return $return;
    }


    // code
    // retourne le code de la réponse
    final public function code():int
    {
        return Base\Header::code($this->headers());
    }


    // protocol
    // retourne le protocol http à partir du header status
    final public function protocol():?string
    {
        return Base\Header::protocol($this->headers());
    }


    // statusText
    // retourne le texte relié à un code status
    final public function statusText():?string
    {
        return Base\Header::statusText($this->headers());
    }


    // status
    // retourne la string header status
    final public function status():?string
    {
        return Base\Header::status($this->headers());
    }


    // contentType
    // retourne le content type à partir des headers
    // si la variable parse n'est pas vide, le content type est envoyé dans header/parseContentType
    // si la variable parse est 2, retourne l'extension
    final public function contentType(?int $parse=1):?string
    {
        return Base\Header::contentType($this->headers(),$parse);
    }


    // contentLength
    // retourne le content length à partir des headers
    final public function contentLength():?int
    {
        return Base\Header::contentLength($this->headers());
    }


    // absolute
    // retourne l'uri absolute de la requête
    final public function absolute(?array $option=null):?string
    {
        return $this->request()->absolute($option);
    }
}
?>