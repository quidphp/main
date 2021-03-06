<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main;
use Quid\Base;

// _overload
// trait that allows a class to retrieve its overloaded version - higher up in the class hierarchy
trait _overload
{
    // getOverloadKey
    // retourne la clé utilisé pour le tableau overload
    final public static function getOverloadKey():string
    {
        $return = static::className();
        $prepend = static::getOverloadKeyPrepend();

        if(!empty($prepend))
        $return = Base\Fqcn::append($prepend,$return);

        return $return;
    }


    // getOverloadKeyPrepend
    // retourne le prepend de la clé à utiliser pour le tableau overload
    public static function getOverloadKeyPrepend():?string
    {
        return null;
    }


    // classOverload
    // retourne la classe à utiliser pour un overload
    final public static function classOverload():string
    {
        return Autoload::getOverload(static::getOverloadKey(),static::class);
    }


    // newOverload
    // retourne une nouvelle instance de la classe mais en utilisant le nom de classe overloader si existant
    public static function newOverload(...$values):self
    {
        $class = static::classOverload();
        $return = new $class(...$values);

        return $return;
    }
}
?>