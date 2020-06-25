<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main;
use Quid\Base;

// _attrPermission
// trait that provides methods to work with permissions in attr
trait _attrPermission
{
    // attrPermissionRef
    // retourne le tableau de la source des paramètres de permission
    // doit retourner une référence
    final protected function &attrPermissionRef():array
    {
        $attr =& $this->attrRef();

        if(!array_key_exists('permission',$attr) || !is_array($attr['permission']))
        $attr['permission'] = [];

        return $attr['permission'];
    }


    // attrPermissionRolesObject
    // retourne les roles par défaut à utiliser
    abstract protected function attrPermissionRolesObject():Roles;


    // onRolePermission
    // permet de spécifier une méthode lors de chaque appel à roleHasPermission
    final protected function onRolePermission($key,array $array):bool
    {
        return true;
    }


    // hasPermission
    // retourne vrai si toutes les permissions sont accordés au rôle par défaut
    final public function hasPermission(...$keys):bool
    {
        $return = false;
        $roles = $this->attrPermissionRolesObject();

        foreach ($keys as $key)
        {
            $return = $this->rolesHasPermission($key,$roles);

            if($return !== true)
            break;
        }

        return $return;
    }


    // checkPermission
    // envoie une exception si la ou les permissions ne sont pas accordés au rôle par défaut
    final public function checkPermission(...$keys):self
    {
        if($this->hasPermission(...$keys) !== true)
        static::throw(...$keys);

        return $this;
    }


    // rolesHasPermission
    // retourne vrai si les rôles peuvent faire l'action
    // si un des roles retourne true, à ce moment c'est true
    final public function rolesHasPermission($key,Roles $roles,bool $exception=true):bool
    {
        $return = false;

        foreach ($roles as $perm => $role)
        {
            if($this->roleHasPermission($key,$role,$exception))
            {
                $return = true;
                break;
            }
        }

        return $return;
    }


    // roleHasPermission
    // retourne vrai si le rôle peut faire l'action
    final public function roleHasPermission($key,Role $role,bool $exception=true):bool
    {
        $return = null;
        $one = $this->getPermission($role);

        if(array_key_exists($key,$one))
        $return = $one[$key];

        if(!is_bool($return))
        {
            if($exception === true)
            static::throw($key,$role);

            else
            $return = false;
        }

        elseif($return === true)
        $return = $this->onRolePermission($key,$one);

        return $return;
    }


    // getPermission
    // retourne le tableau de la source des paramètres d'un rôle
    // les permissions se construisent avec *, ensuite tous les roles retournées par useAlso et ensuite le nom du rôle
    final public function getPermission(Role $role):array
    {
        $return = [];
        $all = $this->attrPermissionRef();
        $loop = ['*'];

        $also = $role->useAlso();
        if(!empty($also))
        $loop = Base\Arr::merge($loop,(array) $also);

        $loop[] = $role->name();

        foreach ($loop as $v)
        {
            if(array_key_exists($v,$all) && is_array($all[$v]))
            $return = Base\Arrs::replace($return,$all[$v]);
        }

        return $return;
    }


    // setPermission
    // permet de changer le contenu d'une permission pour un rôle
    final public function setPermission(array $value,$role=null):void
    {
        $all =& $this->attrPermissionRef();
        $key = '*';

        if($role instanceof Role)
        $key = $role->name();

        elseif(is_string($role))
        $key = $role;

        if(empty($all[$key]))
        $all[$key] = [];

        $all[$key] = Base\Arrs::replace($all[$key],$value);
    }
}
?>