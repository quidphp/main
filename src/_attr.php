<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main;
use Quid\Base;

// _attr
// trait that grants methods to work with the dynamic property attr
trait _attr
{
    // dynamique
    protected $attr = array(); // conserve les attributs
    
    
    // attrRef
    // retourne la référence du tableau des attributs
    protected function &attrRef():array
    {
        return $this->attr;
    }


    // makeAttr
    // méthode appelé pour attributer les attributs
    // si value est true, copie static config
    protected function makeAttr($value,bool $config=true):void
    {
        $attr =& $this->attrRef();
        $merge = array();
        
        if($config === true)
        $merge[] = static::$config;
        
        if(is_array($value))
        $merge[] = $value;
        
        $count = count($merge);
        
        if($count === 2)
        $attr = Base\Arrs::replace(...$merge);
        
        elseif($count === 1)
        $attr = $merge[0];
        
        else
        static::throw();
        
        return;
    }


    // isAttrNotEmpty
    // retourne vrai si l'attribut n'est pas vide
    public function isAttrNotEmpty($key):bool
    {
        return (Base\Validate::isReallyEmpty($this->getAttr($key)))? false:true;
    }
    
    
    // getAttr
    // retourne un attribut
    // possible d'appeler si call est true
    public function getAttr($key,bool $call=false,...$args)
    {
        $return = Base\Arrs::get($key,$this->attrRef());
        
        if($call === true && static::classIsCallable($return))
        $return = Base\Call::withObj($this,$return,...$args);

        return $return;
    }
    
    
    // setAttr
    // permet de changer la valeur d'un attribut
    public function setAttr($key,$value):self
    {
        $attr =& $this->attrRef();
        Base\Arrs::setRef($key,$value,$attr);

        return $this;
    }
    
    
    // attr
    // possible de merger un tableau par dessus les attr
    // retourne les attr
    public function attr(?array $value=null):array
    {
        $return =& $this->attrRef();
        
        if($value !== null)
        $return = Base\Arrs::replace($return,$value);

        return $return;
    }
}
?>