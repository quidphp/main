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
use Quid\Main;

// cache
// class for a cache storage file
class Cache extends Serialize implements Main\Contract\FileStorage
{
    // trait
    use _storage;


    // config
    public static array $config = [
        'dirname'=>'[storageCache]'
    ];
}

// init
Cache::__init();
?>