<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main;
use Quid\Base;

// role
// class that provides basic logic for a role
abstract class Role extends Root
{
    // config
    public static $config = [
        'permission'=>0, // code de permission du rôle
        'can'=>[], // permission générale
        'ignore'=>false, // si le role est ignoré pour roles
    ];


    // cacheStatic
    protected static $cacheStatic = [];


    // construct
    // toutes les méthodes de cet objet sont statiques
    // le paramétrage d'un rôle se fait uniquemment via le tableau config
    // l'objet est seulement utilisé pour faciliter le typage et le passage en argument
    public function __construct()
    {
        return;
    }


    // clone
    // clone est permis
    public function __clone()
    {
        return;
    }


    // toArray
    // retourne le tableau static config
    public function toArray():array
    {
        return static::$config;
    }


    // cast
    // retourne le numéro de la permisison
    public function _cast():int
    {
        return static::permission();
    }


    // serialize
    // serialize une chaîne vide comme l'objet n'a pas de propriété
    public function serialize():string
    {
        return serialize('');
    }


    // unserialize
    // envoie une exception si unserialize n'est pas vide
    public function unserialize($data):self
    {
        $data = unserialize($data);

        if(!empty($data))
        static::throw();

        return $this;
    }


    // isIgnored
    // retourne vrai si la route est ignoré
    public static function isIgnored():bool
    {
        return ((static::$config['ignore'] ?? null) === true)? true:false;
    }


    // isNobody
    // retourne vrai si la permission est nobody
    public static function isNobody():bool
    {
        return (static::permission() <= 1)? true:false;
    }


    // isSomebody
    // retourne vrai si la permission est somebody
    public static function isSomebody():bool
    {
        return (static::permission() > 1)? true:false;
    }


    // permission
    // retourne le code de permission de la classe
    // envoie une exception si le code est 0
    public static function permission():int
    {
        $return = static::$config['permission'] ?? 0;

        if(empty($return))
        static::throw('permissionCodeCannotBeEmpty');

        return $return;
    }


    // name
    // retourne le nom du role, avec lcfirst
    public static function name():string
    {
        return static::className(true);
    }


    // can
    // retourne vrai si le role permet de faire l'action
    // envoie une exception si le retour n'est pas booléean
    public static function can($path):bool
    {
        $return = static::get($path);

        if(!is_bool($return))
        static::throw('invalidReturn',$path);

        return $return;
    }


    // get
    // retourne les données en lien une clé du tableau can de rôle
    public static function get($path)
    {
        return Base\Arrs::get($path,static::$config['can']);
    }


    // validate
    // permet de faire une validation sur la classe role
    public static function validate($value):bool
    {
        $return = false;
        $permission = static::permission();
        $name = static::name();
        $fqcn = static::class;

        if(is_scalar($value))
        $value = [$value];

        if(is_array($value))
        {
            if(Base\Arr::isIndexed($value))
            {
                if(in_array($permission,$value,true) || in_array($fqcn,$value,true) || in_array($name,$value,true))
                $return = true;
            }

            else
            {
                $replace = static::validateReplace();

                if(!empty($replace))
                {
                    $callable = [Base\Number::class,'cast'];
                    $value = Base\Arr::valuesReplace($replace,$value);
                    $value = array_map($callable,$value);

                    if(Base\Validate::isAnd($value,$permission))
                    $return = true;
                }
            }
        }

        return $return;
    }


    // validateReplace
    // retourne un tableau de remplacement pour la validation
    // méthode protégé, à étendre
    protected static function validateReplace():?array
    {
        return null;
    }
}
?>