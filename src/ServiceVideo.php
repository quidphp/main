<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main;
use Quid\Base;

// serviceVideo
// abstract class with basic methods for a service that provides a video object after an HTTP request
abstract class ServiceVideo extends ServiceRequest
{
    // config
    public static $config = [
        'required'=>[], // clés requises
        'video'=>[], // permet de convertir une clé vers une autre, passé comme option dans l'objet vidéo
        'target'=>null
    ];


    // query
    // lance la requête et retourne un objet video en cas de succès
    // plusieurs exceptions peuvent être envoyés
    public function query($value):?Video
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
    public function request($value,?array $option=null):Request
    {
        return static::makeRequest(static::target(['value'=>$value]),Base\Arr::plus($this->option(),$option));
    }


    // makeVideo
    // créer un objet video en utilisation les options de la classe
    public static function makeVideo($value):?Video
    {
        $return = null;
        $option = static::videoOption();
        $required = static::videoRequired();

        if(is_string($value))
        $value = Base\Json::decode($value);

        if(is_array($value) && !empty($value) && Base\Arr::keysExists($required,$value))
        $return = Video::newOverload($value,$option);

        return $return;
    }


    // videoOption
    // retourne les options pour l'objet vidéo
    public static function videoOption():array
    {
        return static::$config['video'];
    }


    // videoRequired
    // retourne les clés requises pour l'objet vidéo
    public static function videoRequired():array
    {
        return static::$config['required'];
    }
}

// config
ServiceVideo::__config();
?>