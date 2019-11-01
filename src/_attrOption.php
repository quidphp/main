<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main;
use Quid\Base;

// _attrOption
// trait that grants methods to work with the dynamic property option
trait _attrOption
{
    // attrOptionRef
    // retourne la réféence du tableau des attributs
    protected function &attrOptionRef():array
    {
        $attr =& $this->attrRef();

        if(!array_key_exists('option',$attr) || !is_array($attr['option']))
        $attr['option'] = [];

        return $attr['option'];
    }


    // getOption
    // retourne une option du tableau d'option
    // possible d'appeler si call est true
    public function getOption($key,bool $call=false,...$args)
    {
        $return = Base\Arrs::get($key,$this->attrOptionRef());

        if($call === true && static::classIsCallable($return))
        $return = Base\Call::withObj($this,$return,...$args);

        return $return;
    }


    // setOption
    // ajoute ou change une option dans le tableau d'option
    public function setOption($key,$value):self
    {
        Base\Arrs::setRef($key,$value,$this->attrOptionRef());

        return $this;
    }


    // option
    // possible de merger un tableau par dessus les options
    // retourne les options
    public function option(?array $value=null):array
    {
        $return =& $this->attrOptionRef();

        if($value !== null)
        $return = Base\Arrs::replace($return,$value);

        return $return;
    }
}
?>