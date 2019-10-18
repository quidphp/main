<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main;
use Quid\Base;

// permission
// trait that provides methods to work with permissions in config or attributes
trait _permission
{
    // permissionAll
    // retourne le tableau de la source des paramètres de permission
    // doit retourner une référence
    abstract protected function &permissionAll():array;


    // permissionDefaultRole
    // retourne le role par défaut à utiliser
    abstract protected function permissionDefaultRole():Role;


    // onPermissionCan
    // permet de spécifier une méthode lors de chaque appel à permissionCan
    protected function onPermissionCan($key,array $array):bool
    {
        return true;
    }


    // permissionRole
    // retourne le tableau de la source des paramètres d'un rôle
    // les permissions se construisent avec *, ensuite tous les roles retournées par useAlso et ensuite le nom du rôle
    public function permissionRole($role):array
    {
        $return = [];
        $all = $this->permissionAll();

        if((is_string($role) && is_a($role,Role::class,true)) || $role instanceof Role)
        {
            $loop = ['*'];

            foreach ((array) $role::useAlso() as $also)
            {
                $loop[] = $also::name();
            }

            $loop[] = $role::name();
        }

        else
        static::throw('invalidRole',$role);

        foreach ($loop as $v)
        {
            if(array_key_exists($v,$all) && is_array($all[$v]))
            $return = Base\Arrs::replace($return,$all[$v]);
        }

        return $return;
    }


    // permissionChange
    // permet de changer le contenu d'une permission pour un rôle
    public function permissionChange(array $value,$role=null):void
    {
        $all =& $this->permissionAll();
        $key = '*';

        if((is_string($role) && is_a($role,Role::class,true)) || $role instanceof Role)
        $key = $role::name();

        elseif(is_string($role))
        $key = $role;

        if(empty($all[$key]))
        $all[$key] = [];

        $all[$key] = Base\Arrs::replace($all[$key],$value);

        return;
    }


    // permissionCan
    // retourne vrai si le rôle peut faire l'action
    public function permissionCan($key,$role,bool $exception=true):bool
    {
        $return = null;
        $one = $this->permissionRole($role);

        if($this->onPermissionCan($key,$one))
        {
            if(array_key_exists($key,$one))
            $return = $one[$key];
        }

        if(!is_bool($return))
        {
            if($exception === true)
            static::throw($key,$role);

            else
            $return = false;
        }

        return $return;
    }


    // hasPermission
    // retourne vrai si toutes les permissions sont accordés
    public function hasPermission(...$keys):bool
    {
        $return = false;
        $role = $this->permissionDefaultRole();

        foreach ($keys as $key)
        {
            $return = $this->permissionCan($key,$role);

            if($return !== true)
            break;
        }

        return $return;
    }


    // checkPermission
    // envoie une exception si la ou les permissions ne sont pas accordés
    public function checkPermission(...$keys):self
    {
        if($this->hasPermission(...$keys) !== true)
        static::throw(...$keys);

        return $this;
    }
}
?>