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

// session
// class for testing Quid\Main\Session
class Session extends Base\Test
{
    // trigger
    final public static function trigger(array $data):bool
    {
        // prepare
        Base\Session::destroy(true,true);
        $boot = $data['boot'];
        $class = $boot->getAttr('assert/fileSession');
        $type = $boot->type();
        $default = ['type'=>$type,'env'=>$boot->env(),'version'=>$boot->version()];

        // construct
        $s = new Main\Session($class,$default);
        assert($s->teardown());
        $s = new Main\Session($class,$default);
        assert($s->setLang('en') === $s);
        $sid = $s->getSid();

        // base
        assert($s->name() === $type);
        assert(strlen($s->getSid()) === 47);
        assert($s->isLang(Base\Lang::default()));
        assert($s->isIp(Base\Request::ip()));
        assert($s->isCsrf($s['csrf']));
        assert(!$s->isCaptcha('12'));
        assert(is_bool($s->isBot()));
        assert($s->getPrefix() === $type.'-');
        assert($s->status() === 2);
        assert($s->getCacheLimiter() === '');
        assert($s->getModule() === 'user');
        assert($s->getSaveHandler() === $s);
        assert($s->getSerializeHandler() === 'php_serialize');
        assert(!empty($s->getSavePath()));
        assert(is_int($s->getLifetime()));
        assert($s->getExpire() > Base\Datetime::now());
        assert(count($s->getGarbageCollect()) === 3);
        assert(count($s->getCookieParams()) === 6);
        assert(count($s->ini()) === 30);
        assert(is_int($s->expire()));
        assert(is_int($s->timestampCurrent()));
        assert($s->timestampPrevious() === null);
        assert($s->timestampDifference() === null);
        assert(is_int($s->requestCount()));
        $s->resetRequestCount();
        assert($s->requestCount() === 1);
        assert($s->userAgent() === Base\Request::userAgent());
        assert($s->ip() === Base\Request::ip());
        assert($s->fingerprint() === null);
        assert($s->lang() === Base\Lang::default());
        assert(is_string($s->csrf()));
        assert($s->refreshCsrf() === null);
        assert($s->refreshCaptcha() === null);
        assert(is_string($s->captcha()));
        assert($s->captcha() === $s->captcha());
        assert($s->captcha() !== $s->captcha(true));
        assert($s->emptyCaptcha() === null);
        assert(null === $s->captcha());
        assert(is_string($s->version()));
        $s->setRemember('username','test');
        assert($s->remember('username') === 'test');
        $s->unsetRemember('username');
        assert($s->remember() === []);
        $s->rememberEmpty();
        assert($s->remember() === null);
        assert(is_string($s->env()));
        assert(is_string($s->type()));
        assert($s->getCaptchaName() === '-captcha-');
        assert($s->getCsrfName() === '-csrf-');

        // onCheckArr
        foreach ($s as $key => $value) { }

        // onStart

        // onEnd

        // onPrepareUnsetInst

        // call

        // isStarted
        assert($s->isStarted());

        // isReady
        assert($s->isReady());

        // checkStarted

        // checkReady

        // getStructure

        // structureFlash

        // structureHistory

        // structureTimeout

        // structureCom

        // hasStorage
        assert($s->hasStorage());

        // storage
        assert($s->storage() instanceof Main\File);

        // info
        assert(count($s->info()) === 19);

        // setLang
        assert($s->setLang('en') === $s);
        assert($s->lang() === 'en');

        // checkSid

        // restart
        $s['bla'] = true;
        assert($s->restart() === $s);
        assert(!empty($sid2 = $s->getSid()));
        assert($sid !== $sid2);
        assert(empty($s['bla']));
        assert($s->toArray() === $_SESSION);

        // regenerateId
        $s['bla'] = true;
        assert($s->regenerateId() === $s);
        assert(!empty($sid3 = $s->getSid()));
        assert($sid2 !== $sid3);
        assert($s['bla'] === true);
        assert($s->toArray() === $_SESSION);

        // encode
        $encode = $s->encode();
        assert(!empty($s->encode()));

        // decode
        assert($s->empty()->isEmpty());
        assert($s->decode($encode)->isNotEmpty());
        assert($s->toArray() === $_SESSION);

        // reset
        $s['bla'] = true;
        assert($s->reset() === $s);
        assert(empty($s['bla']));
        assert($s->toArray() === $_SESSION);

        // abort
        $s['bla'] = true;
        assert(!$s->abort()->isStarted());
        assert($s->start() === $s);
        assert(empty($s['bla']));
        assert($s->toArray() === $_SESSION);

        // commit
        $s['bla'] = true;
        $_SESSION['test'] = 'OK';
        assert($s->commit() === $s);
        assert(!$s->isStarted());
        assert($s->start() === $s);
        assert($s['bla'] === true);
        assert($s['test'] === 'OK');
        assert($s->toArray() === $_SESSION);

        // empty
        assert($s->empty()->isEmpty());
        assert($s->isStarted());
        assert($s->decode($encode)->isNotEmpty());
        assert($s->toArray() === $_SESSION);

        // teardown
        assert(!$s->teardown()->isStarted());
        assert($s->start() === $s);
        $s['ok'] = 2;
        assert($s['ok'] === 2);
        assert($s->toArray() === $_SESSION);

        // garbageCollect
        assert(is_int($s->garbageCollect()));

        // start

        // setDefault

        // getSidDefault

        // history
        assert($s->history() instanceof Main\RequestHistory);

        // historyEmpty
        assert($s->historyEmpty() === $s);

        // timeout
        assert($s->timeout() instanceof Main\Timeout);

        // timeoutEmpty
        assert($s->timeoutEmpty() === $s);

        // com
        assert($s->com() instanceof Main\Com);

        // comEmpty
        assert($s->comEmpty() === $s);

        // flash
        assert($s->flash() instanceof Main\Flash);
        assert($s->flash()->get('blaw') === null);
        assert($s->flash()->set('bla','Ok') instanceof Main\Flash);
        $s['flash']['bla2'] = 'Ok';
        assert($s['flash']['bla2'] === 'Ok');
        assert(!isset($s['flash']['bla2']));
        $s['flash']['bla2'] = 'Ok';
        assert($s->flash()->unset('bla') instanceof Main\Flash);
        assert($s->flash()->exists('bla2'));
        assert($s->flash()->get('bla2') === 'Ok');
        assert($s->flash()->get('bla2') === null);

        // flashEmpty
        assert($s->flashEmpty() === $s);

        // create_sid

        // open

        // validateId

        // read

        // write

        // updateTimestamp

        // close

        // destroy

        // gc

        // inst

        // map
        $s['test'] = 2;
        assert($s['test'] === 2);
        assert($s->get('test') === 2);
        assert($_SESSION['test'] === 2);
        $s['test2/what'] = 2;
        assert($s['test2/what'] === 2);
        assert($s->get('test2/what') === 2);
        assert($_SESSION['test2']['what'] === 2);
        assert($s->isNotEmpty());
        assert(!$s->isEmpty());

        // cleanup
        assert($s->commit() === $s);
        Base\Dir::emptyAndUnlink('[storage]/session');
        $data['boot']->session()->setLang('en');

        return true;
    }
}
?>