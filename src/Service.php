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

// service
// abstract class that provides basic methods to manage a third-party service
abstract class Service extends Root
{
    // config
    protected static array $config = [];


    // dynamique
    protected ?string $serviceKey = null; // enregistre la clé de l'objet, utilisé dans l'objet services


    // construct
    // construit l'objet service
    public function __construct(?array $attr=null)
    {
        $this->makeAttr($attr);

        return;
    }


    // cast
    // cast retourne null
    // un service étendre cette méthode, est utilisé par route pour mettre l'api key de googleMaps dans header
    final public function _cast():?string
    {
        return null;
    }


    // setServiceKey
    // entrepose la clé du service pour l'objet services
    final public function setServiceKey(string $key):void
    {
        $this->serviceKey = $key;

        return;
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


    // docOpenJs
    // élément à ajouter en js dans le docOpen
    public function docOpenJs()
    {
        return;
    }


    // docOpenScript
    // élément à ajouter en script dans le docOpen
    public function docOpenScript()
    {
        return;
    }


    // docCloseScript
    // élément à ajouter en script dans le docClose
    public function docCloseScript()
    {
        return;
    }


    // checkPing
    // vérifie que l'hôte est joignable sur le port spécifié
    // sinon envoie une exception attrapable
    final public static function checkPing(string $host,int $port=80,int $timeout=2):bool
    {
        return Request::checkPing($host,$port,$timeout);
    }


    // getOverloadKeyPrepend
    // retourne le prepend de la clé à utiliser pour le tableau overload
    public static function getOverloadKeyPrepend():?string
    {
        return (static::class !== self::class && !Base\Fqcn::sameName(static::class,self::class))? 'Service':null;
    }
}
?>