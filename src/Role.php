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
class Role extends Root
{
    // trait
    use _serialize;
    
    
    // config
    public static $config = [
        'useAlso'=>null // configuration à partir d'un ou plusieurs autre rôles
    ];


    // dynamique
    protected $permission = null;
    protected $name = null;


    // construct
    // toutes les méthodes de cet objet sont statiques
    // le paramétrage d'un rôle se fait uniquemment via le tableau config
    // l'objet est seulement utilisé pour faciliter le typage et le passage en argument
    public function __construct(string $name,int $permission,?array $attr=null)
    {
        $attr = Base\Arrs::replace(static::$config,$attr);
        $this->makeAttr($attr);
        $this->setName($name);
        $this->setPermission($permission);
        
        return;
    }


    // clone
    // clone est permis
    public function __clone()
    {
        return;
    }


    // cast
    // retourne le numéro de la permisison
    public function _cast():int
    {
        return $this->permission();
    }


    // setPermission
    // conserve le code de permission
    public function setPermission(int $value):void
    {
        $this->permission = $value;
        
        return;
    }
    
    
    // permission
    // retourne le code de permission de la classe
    public function permission():int
    {
        return $this->permission;
    }


    // setName
    // conserve le nom du rôle
    public function setName(string $value):void
    {
        $this->name = $value;
        
        return;
    }
    
    
    // name
    // retourne le nom du role
    public function name():string
    {
        return $this->name;
    }
    
    
    // isNobody
    // retourne vrai si la permission est nobody
    public function isNobody():bool
    {
        return ($this->permission() <= 1)? true:false;
    }


    // isSomebody
    // retourne vrai si la permission est somebody
    public function isSomebody():bool
    {
        return ($this->permission() > 1)? true:false;
    }


    // useAlso
    // retourne un tableau de classe de role compatible avec le role courant
    // est utilisé dans le trait _attrPermission
    public function useAlso()
    {
        return $this->getAttr('useAlso');
    }
    
    
    // roles
    // retourne un objet roles avec le rôle courant dans l'objet
    public function roles():Roles 
    {
        $return = Roles::newOverload();
        $return->add($this);
        
        return $return;
    }
}
?>