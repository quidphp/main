<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main\File;

// audio
// class for an audio file (like mp3)
class Audio extends Binary
{
    // config
    protected static array $config = [
        'group'=>'audio'
    ];
}

// init
Audio::__init();
?>