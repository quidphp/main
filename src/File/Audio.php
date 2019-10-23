<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main\File;

// audio
// class for an audio file (like mp3)
class Audio extends Binary
{
    // config
    public static $config = [
        'group'=>'audio'
    ];
}

// init
Audio::__init();
?>