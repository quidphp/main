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

// roles
// class for a collection containing many roles
class Roles extends MapObj
{
    // trait
    use _inst;
    use Map\_sort;
    use Map\_readOnly;


    // config
    public static $config = [];


    // dynamique
    protected $mapAllow = ['add','unset','remove','filter','sort','serialize','clone']; // méthodes permises
    protected $mapSortDefault = 'permission'; // défini la méthode pour sort par défaut


    // construct
    // renvoie vers le constructeur de mapObj
    final public function __construct(?array $value=null)
    {
        parent::__construct(Role::class,$value);

        return;
    }


    // onPrepareKey
    // prepare une clé pour les méthodes qui soumette une clé
    final protected function onPrepareKey($return)
    {
        if($return instanceof Role)
        $return = $return->permission();

        elseif(is_string($return))
        {
            $names = $this->pair('name');
            $return = Base\Arr::search($return,$names);
        }

        return $return;
    }


    // onPrepareValue
    // si la valeur est un tableau crée un objet
    final protected function onPrepareValue($return)
    {
        if(is_array($return))
        {
            $class = Role::getOverloadClass();
            $return = new $class(...array_values($return));
        }

        return $return;
    }


    // isOne
    // retourne vrai si un des rôles à l'attribut à true
    final public function isOne($value):bool
    {
        $return = false;

        foreach ($this as $role)
        {
            if($role->is($value))
            {
                $return = true;
                break;
            }
        }

        return $return;
    }


    // isAll
    // retourne vrai si tous les rôles ont l'attribut à true
    final public function isAll($value):bool
    {
        $return = false;

        foreach ($this as $role)
        {
            $return = $role->is($value);

            if($return === false)
            break;
        }

        return $return;
    }


    // isNobody
    // retourne vrai si tout les rôles sont nobody
    final public function isNobody():bool
    {
        $return = false;

        foreach ($this as $role)
        {
            $return = $role->isNobody();

            if($return === false)
            break;
        }

        return $return;
    }


    // isSomebody
    // retourne vrai si un des rôle est somebody
    final public function isSomebody():bool
    {
        $return = false;

        foreach ($this as $role)
        {
            if($role->isSomebody())
            {
                $return = true;
                break;
            }
        }

        return $return;
    }


    // add
    // ajoute un ou plusieurs objets roles dans l'objet
    // deux objets ne peuvent pas avoir le même nom ou la même permission
    final public function add(...$values):self
    {
        $this->checkAllowed('add');
        $values = $this->prepareValues(...$values);
        $data =& $this->arr();
        $names = $this->pair('name');

        foreach ($values as $value)
        {
            if(!$value instanceof Role)
            static::throw('requires Role');

            $permission = $value->permission();
            $name = $value->name();

            if(array_key_exists($permission,$data))
            static::throw('permissionAlreadyIn',$permission);

            if(in_array($name,$names,true))
            static::throw('nameAlreadyIn',$name);

            $data[$permission] = $value;
        }

        return $this->checkAfter();
    }


    // nobody
    // retorne le premier role nobody
    final public function nobody():?Role
    {
        return $this->first(['isNobody'=>true]);
    }


    // main
    // retourne le rôle avec la plus grande permission
    // ceci est considéré comme le rôle principale
    final public function main():?Role
    {
        $return = null;
        $data = $this->arr();

        if(!empty($data))
        {
            krsort($data);
            $return = current($data);
        }

        return $return;
    }


    // makeFromArray
    // construit un objet roles à partir d'un tableau
    final public static function makeFromArray(array $array):self
    {
        $return = static::newOverload();

        foreach ($array as $name => $args)
        {
            if(is_int($args))
            $args = [$args];

            if(is_string($name) && is_array($args))
            {
                $args = Base\Arr::append($name,$args);
                $return->add($args);
            }

            else
            static::throw();
        }

        return $return;
    }
}

// init
Roles::__init();
?>