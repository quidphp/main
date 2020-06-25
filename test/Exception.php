<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Test\Main;
use Quid\Base;
use Quid\Main;

// exception
// class for testing Quid\Main\Exception
class Exception extends Base\Test
{
    // trigger
    final public static function trigger(array $data):bool
    {
        // prepare
        $boot = $data['boot'];
        $enFile = $boot->getAttr('assert/langFile/en');
        $lang = new Main\Lang(['en','fr']);
        $lang->changeLang('en')->overwrite($enFile);
        $i = new \Exception('base');
        $e = new Main\Exception('test');
        $exception = new Main\Exception('exception!');
        $exception2 = new Main\Exception('deuxieme',$exception);
        $arg = new Main\Exception('message!',null,null,'caRoule',['string'],3,'james');

        // construct

        // invoke

        // toString

        // cast
        assert(!empty($e->_cast()));

        // setArgs

        // args
        assert($arg->args() === ['caRoule',['string'],3,'james']);

        // messageArgs
        assert($arg->messageArgs()['key'] === ['exception','exception','caRoule']);
        assert($arg->messageArgs()['replace'][3] === 'james');

        // getMessageArgs
        assert($arg->getMessageArgs($lang) === 'What !!! [1] 3 james [4]');
        assert($arg->getMessageArgs() === 'message!');

        // content
        assert($e->content() === null);

        // error
        assert($e->error() instanceof Main\Error);

        // trigger

        // echoOutput

        // getOutput
        assert(is_string($e->getOutput()));

        // log
        assert($e->log() === $e);

        // com

        // throw

        // stack
        assert(count(Main\Exception::stack($exception)) === 0);
        assert(count(Main\Exception::stack($exception2)) === 1);

        // output
        assert(!empty(Main\Exception::output($exception)));

        // staticCatched
        assert(Main\Exception::staticCatched($i) instanceof Main\Error);

        // staticToArray
        assert(count(Main\Exception::staticToArray($i)) === 3);

        // exception
        assert(!$e instanceof Main\Contract\Catchable);
        assert($e->getCode() === 31);

        return true;
    }
}
?>