<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main\File;
use Quid\Base;

// json
// class for a json file
class Json extends Text
{
    // config
    protected static array $config = [
        'group'=>'json',
        'read'=>[
            'callback'=>[Base\Json::class,'decode']],
        'write'=>[
            'callback'=>[Base\Json::class,'encodePretty']]
    ];


    // readGet
    // permet de faire une lecture et retourner seulement une valeur de l'objet json
    final public function readGet($key=null)
    {
        $return = null;
        $source = $this->read();

        if(is_array($source))
        $return = Base\Arrs::get($key,$source);

        return $return;
    }
}

// init
Json::__init();
?>