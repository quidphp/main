<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main;
use Quid\Base;

// serviceRequest
// abstract class with basic methods for a service that works with HTTP request
abstract class ServiceRequest extends Service
{
    // config
    protected static array $config = [
        'target'=>null, // cible du service
        'ping'=>2,
        'responseCode'=>200
    ];


    // target
    // retourne la target du service
    // envoie une exception si vide
    final public static function target(?array $replace=null,?string $key=null):string
    {
        $return = static::$config['target'] ?? null;

        if(is_string($key) && is_array($return))
        $return = $return[$key] ?? null;

        if(is_string($return) && !empty($return) && !empty($replace))
        {
            $replace = Base\Arr::keysWrap('%','%',$replace);
            $return = Base\Str::replace($replace,$return);
        }

        return $return;
    }


    // makeRequest
    // retourne un nouvel objet requête
    // utilise la classe requête dans requestClass et les attrs dans requestOption
    final protected static function makeRequest($value,array $attr):Request
    {
        $return = null;
        $class = static::requestClass();

        if(empty($attr['userAgent']))
        $attr['userAgent'] = static::userAgent();

        $return = new $class($value,$attr);

        return $return;
    }


    // requestClass
    // retourne la classe à utiliser pour request
    final protected static function requestClass():string
    {
        return Request::classOverload();
    }


    // userAgent
    // retourne le userAgent à utiliser s'il n'est pas spécifié dans attr
    public static function userAgent():string
    {
        return 'QUID/'.Base\Server::quidVersion();
    }


    // getOverloadKeyPrepend
    // retourne le prepend de la clé à utiliser pour le tableau overload
    public static function getOverloadKeyPrepend():?string
    {
        return (static::class !== self::class && !Base\Fqcn::sameName(static::class,self::class))? 'Service':null;
    }
}

// init
ServiceRequest::__init();
?>