<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main\File;

// video
// class for a video file (like mp4)
class Video extends Binary
{
    // config
    protected static array $config = [
        'group'=>'video'
    ];
}

// init
Video::__init();
?>