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

// error
// class for testing Quid\Main\Error
class Error extends Base\Test
{
    // trigger
    final public static function trigger(array $data):bool
    {
        // construct
        $error = new Main\Error(['test',__FILE__,$line = __LINE__,E_USER_WARNING],1);
        $notice = new Main\Error(['test',__FILE__,__LINE__],2);
        $exception = new Main\Exception('numero1');
        $exception2 = new Main\Exception('numero2',$exception);
        $exception3 = new Main\Exception('numero3',$exception2);
        $silent = new Main\Error(['message'=>'silentz','file'=>__FILE__,'line'=>__LINE__],21);
        $warning = new Main\Error(['warningz',__FILE__,__LINE__],22);
        $fatal = new Main\Error(['fatalz',__FILE__,__LINE__],23);
        $e = new Main\Error('ok');
        $ex = new Main\Error($exception);
        $ex2 = new Main\Error($exception2);
        assert(!empty($e->toJson()));
        assert(!empty(serialize($e)));

        // invoke

        // toString

        // toArray
        assert(count($e->toArray()) === 5);
        assert(count($e->toArray()['trace']) > 1);

        // cast
        assert($e->_cast() === 'Warning (#22) -> ok');

        // isException
        assert(!$e->isException());
        assert($ex->isException());

        // id
        assert(strlen($e->id()) > 11);

        // basename
        assert(strlen($e->basename()) > 25);

        // makeSilent

        // getMessage
        assert($error->getMessage() === 'test');

        // setMessage

        // getCode
        assert($error->getCode() === 1);

        // setCode
        assert($e->getCode() === 22);

        // getFile
        assert(__FILE__ === $error->getFile());
        assert($e->getFile() === __FILE__);
        assert($warning->getFile() === __FILE__);

        // setFile

        // getLine
        assert($line === $error->getLine());

        // setLine

        // getTrace
        assert(count($e->getTrace(3,true)) === 3);
        assert(count($e->getTrace(3,false)) === 3);

        // getTraceLastCall
        assert(Main\Error::class.'::__construct' === $error->getTraceLastCall());

        // setTrace

        // getInfo
        assert($error->getInfo() === 512);

        // setInfo

        // getStack
        assert($error->getStack() === null);
        assert($ex->getStack() === []);
        assert(count($ex2->getStack()) === 1);

        // setStack

        // getContent
        assert($ex2->getContent() === null);

        // setContent

        // prepare

        // prepareThrowable

        // getType
        assert(count($error->getType()) === 2);

        // getKey
        assert('error' === $error->getKey());
        assert('warning' === $warning->getKey());

        // title
        assert($ex->title() === 'Exception: Quid\Main\Exception (#31)');
        assert($error->title() === 'Error: E_USER_WARNING (#1)');

        // titleMessage
        assert($error->titleMessage() === 'Error: E_USER_WARNING (#1) -> test');

        // name
        assert($error->name() === 'Error');

        // trigger
        $fatal->setAttr('callback',fn($error) => $error->makeSilent());
        $fatal->trigger();

        // dispatch

        // errorLog

        // log

        // com

        // getOutputMode
        assert(in_array($error->getOutputMode(),['html','cli'],true));

        // output

        // getOutput
        assert(is_string($error->getOutput()));

        // outputCli

        // cli
        assert(strlen($error->cli()) > 155);

        // outputHtml

        // outputJson

        // html
        assert(Base\Html::is($error->html()));
        assert(is_string($error->html()));
        assert(!empty($silent->html()));
        $x = new Main\Error($e->toArray(),null,['outputDepth'=>7]);
        $y = new Main\Error($x->toArray(),null,['outputDepth'=>7]);
        assert($x->html() !== $y->html()); // id est différent

        // json
        assert(count($error->json()) === 5);

        // makeOutputArray
        assert(count($warning->makeOutputArray()) === 5);
        assert($warning->makeOutputArray()[1] === 'Warning (#22)');
        assert($fatal->makeOutputArray()[2] === '«fatalz»');
        assert(count($warning->makeOutputArray(false)) === 4);

        // getOutputArray
        assert(count($error->getOutputArray()) === 3);

        // attr
        assert(count($error->attr()) === 22);

        // handler

        // exception

        // assert

        // make

        // silent

        // warning

        // fatal

        // defaultCode
        assert(Main\Error::defaultCode() === 22);

        // grabCode
        assert(1 === Main\Error::grabCode(123));
        assert(22 === Main\Error::grabCode('default'));
        assert(2 === Main\Error::grabCode(E_USER_NOTICE));
        assert(3 === Main\Error::grabCode(E_USER_DEPRECATED));
        assert(11 === Main\Error::grabCode('assert'));
        assert(21 === Main\Error::grabCode('silent'));
        assert(22 === Main\Error::grabCode('warning'));
        assert(23 === Main\Error::grabCode('fatal'));
        assert(34 === Main\Error::grabCode(new \LogicException('test',34)));
        assert(31 === Main\Error::grabCode(new \LogicException('test')));
        assert(31 === Main\Error::grabCode(new Main\Exception('test')));

        // getLang

        // setLang

        // getCom

        // setCom

        // boolToOutputDepth

        // setDefaultOutputDepth

        // getTraceLength
        assert(is_int(Main\Error::getTraceLength()));

        // setDocHead

        // init

        return true;
    }
}
?>