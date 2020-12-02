<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main;
use Quid\Base;

// role
// class that provides basic logic for a role
class Role extends Root
{
    // trait
    use _serialize;


    // config
    protected static array $config = [
        'useAlso'=>null // configuration à partir d'un ou plusieurs autre rôles
    ];


    // dynamique
    protected int $permission;
    protected string $name;


    // construct
    // toutes les méthodes de cet objet sont statiques
    // le paramétrage d'un rôle se fait uniquemment via le tableau config
    // l'objet est seulement utilisé pour faciliter le typage et le passage en argument
    final public function __construct(string $name,int $permission,?array $attr=null)
    {
        $attr = Base\Arrs::replace(static::$config,$attr);
        $this->makeAttr($attr);
        $this->setName($name);
        $this->setPermission($permission);
    }


    // clone
    // clone est permis
    final public function __clone()
    {
        return;
    }


    // cast
    // retourne le numéro de la permisison
    final public function _cast():int
    {
        return $this->permission();
    }


    // setPermission
    // conserve le code de permission
    final public function setPermission(int $value):void
    {
        $this->permission = $value;
    }


    // permission
    // retourne le code de permission de la classe
    final public function permission():int
    {
        return $this->permission;
    }


    // setName
    // conserve le nom du rôle
    final public function setName(string $value):void
    {
        $this->name = $value;
    }


    // name
    // retourne le nom du role
    final public function name():string
    {
        return $this->name;
    }


    // is
    // retourne vrai si le role a l'attribut true
    final public function is($value):bool
    {
        return $this->getAttr($value) === true;
    }


    // isNobody
    // retourne vrai si la permission est nobody
    final public function isNobody():bool
    {
        return $this->permission() <= 1;
    }


    // isSomebody
    // retourne vrai si la permission est somebody
    final public function isSomebody():bool
    {
        return $this->permission() > 1;
    }


    // output
    // permet de faire un output du rôle (permission et name)
    public function output():array
    {
        return [
            'permission'=>$this->permission(),
            'name'=>$this->name()
        ];
    }


    // useAlso
    // retourne un tableau de classe de role compatible avec le role courant
    // est utilisé dans le trait _attrPermission
    final public function useAlso()
    {
        return $this->getAttr('useAlso');
    }


    // roles
    // retourne un objet roles avec le rôle courant dans l'objet
    final public function roles():Roles
    {
        $return = Roles::newOverload();
        $return->add($this);

        return $return;
    }
}
?>