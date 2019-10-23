<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main\File;

// video
// class for a video file (like mp4)
class Video extends Binary
{
    // config
    public static $config = [
        'group'=>'video'
    ];
}

// init
Video::__init();
?>