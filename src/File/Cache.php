<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main\File;
use Quid\Main;

// cache
// class for a cache storage file
class Cache extends Serialize implements Main\Contract\FileStorage
{
    // trait
    use _storage;


    // config
    protected static array $config = [
        'dirname'=>'[storageCache]'
    ];
}

// init
Cache::__init();
?>