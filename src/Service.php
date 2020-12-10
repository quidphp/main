<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main;
use Quid\Base;

// service
// abstract class that provides basic methods to manage a third-party service
abstract class Service extends Root
{
    // config
    protected static array $config = [
        'serviceType'=>'default'
    ];


    // dynamique
    protected ?string $serviceKey = null; // enregistre la clé de l'objet, utilisé dans l'objet services


    // construct
    // construit l'objet service
    public function __construct(?array $attr=null)
    {
        $this->makeAttr($attr);
    }


    // cast
    // cast retourne null
    // un service étendre cette méthode, est utilisé par route pour mettre l'api key de googleMaps dans header
    final public function _cast():?string
    {
        return null;
    }


    // isServiceType
    // retourne vrai si le type de service est celui donné en argument
    final public function isServiceType($value):bool
    {
        return $this->getServiceType() === $value;
    }


    // setServiceKey
    // entrepose la clé du service pour l'objet services
    final public function setServiceKey(string $key):void
    {
        $this->serviceKey = $key;
    }


    // getServiceKey
    // retourne la clé du service pour l'objet services
    // peut retourner null
    final public function getServiceKey(bool $required=false):?string
    {
        $return = $this->serviceKey;

        if($required === true && !is_string($return))
        static::throw('keyMissing');

        return $return;
    }


    // getServiceType
    // retourne le type de service
    final public function getServiceType():string
    {
        return $this->getAttr('serviceType');
    }


    // checkPing
    // vérifie que l'hôte est joignable sur le port spécifié
    // sinon envoie une exception attrapable
    final public static function checkPing(string $host,int $port=80,int $timeout=2):bool
    {
        $request = Request::newOverload(['scheme'=>'http','host'=>$host,'port'=>$port]);
        return $request->checkPing();
    }


    // getOverloadKeyPrepend
    // retourne le prepend de la clé à utiliser pour le tableau overload
    public static function getOverloadKeyPrepend():?string
    {
        return (static::class !== self::class && !Base\Fqcn::sameName(static::class,self::class))? 'Service':null;
    }
}
?>