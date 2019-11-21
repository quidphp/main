<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 * Readme: https://github.com/quidphp/main/blob/master/README.md
 */

namespace Quid\Test\Main;
use Quid\Base;
use Quid\Main;

// video
// class for testing Quid\Main\Video
class Video extends Base\Test
{
    // trigger
    final public static function trigger(array $data):bool
    {
        // construct
        $data = ['abs'=>'http://google.com','namez'=>'LOL','description'=>'OK','html'=>'<div></div>'];
        $video = new Main\Video($data,['absolute'=>'abs','name'=>'namez']);

        // toString

        // grab

        // name
        assert($video->name() === 'LOL');

        // date
        assert($video->date() === null);

        // description
        assert($video->description() === 'OK');

        // absolute
        assert($video->absolute() === 'http://google.com');

        // thumbnail
        assert($video->thumbnail() === null);

        // html
        assert($video->html() === '<div></div>');

        // input
        assert($video->input() === null);

        return true;
    }
}
?>