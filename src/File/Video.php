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

// video
// class for a video file (like mp4)
class Video extends Binary
{
    // config
    public static array $config = [
        'group'=>'video'
    ];
}

// init
Video::__init();
?>