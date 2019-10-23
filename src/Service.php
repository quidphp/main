<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main;
use Quid\Base;

// service
// abstract class that provides basic methods to manage a third-party service
abstract class Service extends Root
{
    // trait
    use _option;


    // config
    public static $config = [];


    // dynamique
    protected $key = null; // enregistre la clé de l'objet


    // construct
    // construit l'objet service
    public function __construct(string $key,?array $option=null)
    {
        $this->setKey($key);
        $this->option($option);

        return;
    }


    // cast
    // cast retourne null
    // un service étendre cette méthode, est utilisé par route pour mettre l'api key de googleMaps dans header
    public function _cast():?string
    {
        return null;
    }


    // setKey
    // entrepose la clé du service
    public function setKey(string $key):void
    {
        $this->key = $key;

        return;
    }


    // getKey
    // retourne la clé du service
    public function getKey():string
    {
        return $this->key;
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
    public static function checkPing(string $host,int $port=80,int $timeout=2):bool
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