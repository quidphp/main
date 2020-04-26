<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 * Readme: https://github.com/quidphp/main/blob/master/README.md
 */

namespace Quid\Main\File;
use Quid\Base;

// serialize
// class for a file with content that should be serialized
class Serialize extends Text
{
    // config
    public static array $config = [
        'group'=>null,
        'read'=>[
            'callback'=>[Base\Crypt::class,'unserialize']],
        'write'=>[
            'callback'=>[Base\Crypt::class,'serialize']]
    ];
}

// init
Serialize::__init();
?>