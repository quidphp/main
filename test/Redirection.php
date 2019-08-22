<?php
declare(strict_types=1);
namespace Quid\Main\Test;
use Quid\Main;
use Quid\Base;

// redirection
class Redirection extends Base\Test
{
	// trigger
	public static function trigger(array $data):bool
	{
		// prepare
		$request = new Main\Request('/jamesReq');
		$request2 = new Main\Request('jamesReq2');
		$r = new Main\Redirection(['/test.jpg'=>'/test2.jpg','/james/ok'=>'https://google.com']);
		assert($r->set($request,$request2)->isCount(3));
		assert($r->set("james/james2/*","https://google.com/james/james3/*")->isCount(4));

		// onPrepareKey

		// onPrepareValue

		// onPrepareReplace

		// exists
		assert(!$r->exists('james/james2/jamesOK/LOL','/test.jpg','Ok'));
		assert($r->exists('james/james2/jamesOK/LOL','/test.jpg','/james/ok'));

		// get
		assert($r->get('james/james2/jamesOK/LOL') === 'https://google.com/james/james3/jamesOK/LOL');

		// gets
		assert(count($r->gets('james/james2/jamesOK/LOL','/test.jpg','Ok')) === 3);

		// trigger
		assert($r->set('//80.10.1.1','https://google.com') === $r);
		assert($r->set('//80.10.1.2*','https://google.com*') === $r);
		assert($r->trigger('//80.10.1.3') === null);

		// set
		assert($r->set('james','james2')->isCount(7));
		assert($r->get('http://80.10.1.1') === 'https://google.com');
		assert($r->get('https://80.10.1.1') === 'https://google.com');
		assert($r->get('https://80.10.1.1') === 'https://google.com');
		assert($r->get('https://80.10.1.2/z') === 'https://google.com/z');
		assert($r->get('//80.10.1.2/z') === 'https://google.com/z');

		// makeOverwrite
		assert($r->overwrite($r)->isCount(7));

		// map
		assert(count($r->keys()) === 7);
		assert(count($r->values()) === 7);
		assert($r->get($request) === $request2->absolute());
		assert($r->in($request2));
		assert($r->empty()->isEmpty());
		
		return true;
	}
}
?>