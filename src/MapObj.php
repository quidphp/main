<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main;

// mapObj
// class for a collection containing objects
class MapObj extends Map
{
    // trait
    use Map\_obj;


    // config
    protected static array $config = [];


    // construct
    // renvoie au constructeur parent, mais la classe est mise dans is
    public function __construct(string $class,$data=null)
    {
        $this->mapIs = $class;
        parent::__construct($data);
    }
}
?>