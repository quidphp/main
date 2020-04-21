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

// video
// class for an object representing a video with some meta-data
class Video extends Map
{
    // config
    public static $config = [
        'name'=>'name',
        'date'=>'date',
        'description'=>'description',
        'absolute'=>'absolute',
        'thumbnail'=>'thumbnail',
        'html'=>'html'
    ];


    // dynamique
    protected $mapAllow = ['overwrite','serialize','clone']; // méthodes permises


    // construct
    // créer l'objet video, le premier argument doit être un tableau ou une chaîne json
    final public function __construct($value,?array $attr=null)
    {
        $this->makeAttr($attr);

        if(is_string($value))
        $value = Base\Json::decode($value);

        if(is_array($value))
        $this->overwrite($value);

        else
        static::throw('requires','jsonStringOrArray');

        return;
    }


    // toString
    // affiche l'objet comme string, retourne le html
    final public function __toString():string
    {
        return $this->html();
    }


    // grab
    // retourne une valeur de la map, en utilisation la clé tel que défini dans attr
    final public function grab(string $key)
    {
        $return = null;
        $realKey = $this->getAttr($key);

        if(is_string($realKey))
        $return = $this->get($realKey);

        elseif(static::isCallable($realKey))
        $return = $realKey($this);

        return $return;
    }


    // name
    // retourne le nom de la vidéo
    // est facultatif
    final public function name():?string
    {
        $return = null;
        $name = $this->grab('name');

        if(is_string($name))
        $return = $name;

        return $return;
    }


    // date
    // retourne la date de la vidéo
    // possible de spécifier un format
    // est facultatif
    final public function date($format=null)
    {
        $return = null;
        $date = $this->grab('date');

        if(is_string($date))
        {
            $date = Base\Datetime::time($date,'sql');

            if(is_int($date))
            {
                $return = $date;

                if(is_scalar($format))
                $return = Base\Datetime::format($format,$return);
            }
        }

        return $return;
    }


    // description
    // retourne la description de la vidéo
    // possible de spécifier une longueur d'excerpt
    // est facultatif
    final public function description(?int $length=null):?string
    {
        $return = null;
        $description = $this->grab('description');

        if(is_string($description))
        {
            $return = $description;

            if(is_int($length))
            $return = Base\Str::excerpt($length,$return);
        }

        return $return;
    }


    // absolute
    // retourne le lien absolue de la vidéo
    // est obligatoire
    final public function absolute():string
    {
        $return = null;
        $absolute = $this->grab('absolute');

        if(is_string($absolute))
        $return = Base\Uri::absolute($absolute);

        return $return;
    }


    // thumbnail
    // retourne le lien vers le thumbnail de la vidéo
    // est facultatif
    final public function thumbnail():?string
    {
        $return = null;
        $thumbnail = $this->grab('thumbnail');

        if(is_string($thumbnail))
        $return = Base\Uri::absolute($thumbnail);

        return $return;
    }


    // html
    // retourne le code html de la vidéo
    // est obligatoire
    final public function html():string
    {
        $return = null;
        $html = $this->grab('html');

        if(is_string($html))
        $return = $html;

        return $return;
    }


    // input
    // retourne la string input si existante
    final public function input():?string
    {
        return $this->get('input');
    }
}
?>