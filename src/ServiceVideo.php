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

// serviceVideo
// abstract class with basic methods for a service that provides a video object after an HTTP request
abstract class ServiceVideo extends ServiceRequest
{
    // config
    protected static array $config = [
        'required'=>[], // clés requises
        'video'=>[], // permet de convertir une clé vers une autre, passé comme option dans l'objet vidéo
        'hostsValid'=>[], // hosts valide pour le service video
        'target'=>null
    ];


    // query
    // lance la requête et retourne un objet video en cas de succès
    // plusieurs exceptions peuvent être envoyés
    final public function query($value):?Video
    {
        $return = null;
        $request = $this->request($value);

        $response = $request->trigger();
        $json = $response->body(true);

        if(is_array($json) && !empty($json))
        {
            $json['input'] = $value;
            $return = static::makeVideo($json);
        }

        if($return === null)
        static::catchable(null,'invalidResponseFormat');

        return $return;
    }


    // request
    // retourne la requête à utiliser pour aller chercher l'objet video
    final public function request($value,?array $option=null):Request
    {
        return static::makeRequest(static::target(['value'=>$value]),Base\Arr::plus($this->attr(),$option));
    }


    // isValidInput
    // retourne vrai si le service est compatible avec le input
    // pour ce faire compare les hosts
    final public static function isValidInput($value):bool
    {
        $return = false;

        if(is_string($value))
        {
            $host = Base\Uri::host($value);
            if(!empty($host) && in_array($host,static::$config['hostsValid'],true))
            $return = true;
        }

        return $return;
    }


    // makeVideo
    // créer un objet video en utilisation les options de la classe
    final public static function makeVideo(array $value):?Video
    {
        $return = null;
        $option = static::videoOption();
        $required = static::videoRequired();

        if(Base\Arr::keysExists($required,$value))
        $return = Video::newOverload($value,$option);

        return $return;
    }


    // videoOption
    // retourne les options pour l'objet vidéo
    final public static function videoOption():array
    {
        return static::$config['video'];
    }


    // videoRequired
    // retourne les clés requises pour l'objet vidéo
    final public static function videoRequired():array
    {
        return static::$config['required'];
    }


    // getOverloadKeyPrepend
    // retourne le prepend de la clé à utiliser pour le tableau overload
    final public static function getOverloadKeyPrepend():?string
    {
        return (static::class !== self::class && !Base\Fqcn::sameName(static::class,self::class))? 'Service':null;
    }
}

// init
ServiceVideo::__init();
?>