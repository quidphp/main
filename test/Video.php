<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/test/blob/master/LICENSE
 */

namespace Quid\Test\Main;
use Quid\Main;
use Quid\Base;

// video
// class for testing Quid\Main\Video
class Video extends Base\Test
{
	// trigger
	public static function trigger(array $data):bool
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