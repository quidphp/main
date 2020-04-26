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

// localization
// class for storing localization data, like latitude and longitude
class Localization extends Map
{
    // config
    public static array $config = [];


    // map
    protected ?array $mapAllow = ['overwrite','jsonSerialize','serialize','clone']; // méthodes permises


    // construct
    // construit l'objet de localization
    // une string json ou un array doit être fourni
    final public function __construct($value)
    {
        if(is_string($value))
        $value = Base\Json::decode($value);

        if(is_array($value))
        $this->overwrite($value);

        else
        static::throw('requires','jsonStringOrArray');

        return;
    }


    // toString
    // affiche l'objet comme string, retourne la string input
    final public function __toString():string
    {
        return $this->input();
    }


    // onPrepareReplace
    // prépare le tableau de remplacement en vue d'un overwrite
    // une exception sera envoyé si le tableau n'est pas dans le bon format
    final protected function onPrepareReplace($value)
    {
        $return = null;

        if(is_array($value) && Base\Arr::keysExists(['lat','lng','countryCode','input'],$value))
        {
            if(!(is_numeric($value['lat']) && is_numeric($value['lng'])))
            static::throw('invalidLatLng');

            if(!(is_string($value['countryCode']) && strlen($value['countryCode']) === 2))
            static::throw('invalidCountryCode');

            if(is_string($value['input']))
            $return = $value;
        }

        if(!is_array($return))
        static::throw('invalidFormat');

        return $return;
    }


    // inUsa
    // retourne vrai si le pays de la localization est USA
    final public function inUsa()
    {
        return strtoupper($this->countryCode()) === 'US';
    }


    // lat
    // retourne la valeur lat sous forme de float
    final public function lat():float
    {
        return Base\Num::cast($this->get('lat'));
    }


    // lng
    // retourne la valeur lng sous forme de float
    final public function lng():float
    {
        return Base\Num::cast($this->get('lng'));
    }


    // latLng
    // retourne le tableau latlng
    final public function latLng():array
    {
        return ['lat'=>$this->lat(),'lng'=>$this->lng()];
    }


    // input
    // retourne la string input si existante
    final public function input():?string
    {
        return $this->get('input');
    }


    // countryCode
    // retourne le code de pays
    final public function countryCode():string
    {
        return $this->get('countryCode');
    }
}
?>