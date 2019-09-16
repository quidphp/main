<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Test\Main;
use Quid\Base;
use Quid\Main;

// requestHistory
// class for testing Quid\Main\RequestHistory
class RequestHistory extends Base\Test
{
    // trigger
    public static function trigger(array $data):bool
    {
        // prepare
        $request = new Main\Request('/test.jpg');
        $request2 = new Main\Request(Base\Request::export());
        $request3 = new Main\Request('http://google.com');
        $request4 = new Main\Request('/testbla|.jpg');
        $request4->setMethod('post');
        $rh = new Main\RequestHistory();
        $rh->add($request2);
        $rh->add($request3);
        $rh->add($request4);
        $rh->add($request);
        assert($rh->add($request2) === $rh);

        // onPrepareValue

        // onPrepareReturns

        // cast
        assert($rh->_cast() === 5);

        // hasUri
        assert($rh->hasUri('http://google.com'));
        assert(!$rh->hasUri('https://google.com'));

        // hasCurrentUri
        assert($rh->hasCurrentUri());

        // add
        assert($rh->add($request3)->isCount(6));
        assert($rh->add($request2)->isCount(7));

        // addUnique
        assert($rh->addUnique($request2)->isCount(5));

        // previous
        assert($rh->previous()['absolute'] === 'http://google.com');

        // previousRequest
        assert($rh->previousRequest()->absolute() === 'http://google.com');

        // absolute
        assert(Base\Arr::isUni($rh->absolute()));

        // request
        assert($rh->request()[0] instanceof Main\Request);

        // all
        assert(count($rh->all()) === 5);
        assert(count($rh->all()[0]) === 4);

        // extra
        assert(Main\RequestHistory::extra() === ['redirectable'=>true]);
        assert(Main\RequestHistory::extra(true) === ['redirectable']);

        // isArrayValid
        assert(!Main\RequestHistory::isArrayValid(['Asd']));

        // map
        assert($rh->in($request4));
        assert(!$rh->in(new Main\Request('bla.com')));

        // nav
        assert($rh->pageSlice(1,2)->isCount(2));
        assert($rh->pageFirst(2) === 1);
        assert($rh->pagePrev(2,2) === 1);
        assert($rh->pageNext(2,2) === 3);
        assert($rh->pageLast(2) === 3);
        assert(count($rh->general(1,2)) === 9);

        return true;
    }
}
?>