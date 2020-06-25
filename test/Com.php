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

// com
// class for testing Quid\Main\Com
class Com extends Base\Test
{
    // trigger
    final public static function trigger(array $data):bool
    {
        // prepare
        $boot = $data['boot'];
        $fr = $boot->getAttr('assert/lang/fr');
        $en = $boot->getAttr('assert/lang/en');
        $lang = new Main\Lang(['en','fr']);
        $lang->changeLang('fr')->overwrite($fr::config());
        $lang->changeLang('en')->overwrite($en::config());

        // construct
        $com = new Main\Com();
        $com2 = new Main\Com();
        $com3 = new Main\Com();
        $com4 = new Main\Com();
        $comAttr = new Main\Com();

        // toString

        // onPrepareValue

        // cast
        assert($com4->_cast() === '');

        // is
        assert($com->is(['pos','ok']));
        assert(!$com->is([null,'ok']));

        // isType
        assert($com->isType('neg'));
        assert(!$com->isType('negz'));

        // checkType
        assert($com->checkType('neg'));

        // type
        assert($com->type() === 'neg');
        assert($com->type('posz') === 'posz');

        // getType
        assert($com->getType() === ['neg','pos','neutral']);

        // setType

        // payload
        assert($com->payload('neg',['login','loginAttempt'],null) === ['neg','login/loginAttempt',null,null]);
        assert($com->payload('neg','login/ok',null,null,[]) === ['neg','login/ok',null,null]);
        assert($com->payload('neutral','row/1',null,null,['neg','no change',null,null,['pos','tryAgain']])[4][0][4][0] === ['pos','tryAgain',null,null]);

        // findCommon

        // update

        // unshift

        // push

        // append
        assert($com->append(null,['login','userInactive']) === $com);
        assert($com->append('neg','login/alreadyConnected') === $com);
        assert($com->in(['neg','login/alreadyConnected']));
        assert($com->in(['neg',['login/alreadyConnected']]));
        assert($com->in(['neg',['login','alreadyConnected']]));
        assert($com->search(['neg','login/alreadyConnected']) === 1);
        assert($com->search(['neg',['login','alreadyConnected']]) === 1);
        assert($com->search(['neg','login/alreadyConnectedzzz']) === null);
        assert(!empty($com->toArray()));
        assert(!empty($com->toJson()));

        // prepend
        assert($com->prepend(null,'login/cantFindUser') === $com);

        // pos
        assert($com->pos(['login','success']) === $com);
        assert($com2->pos('Ça passe, tout est OK') === $com2);

        // posPrepend
        assert($com3->posPrepend('noway!') === $com3);

        // neg
        assert($com->neg(['login/alreadyConnected']) === $com);
        assert($com2->neg(['Grosse erreur']) === $com2);
        assert($com2->neg(['Grosse','erreur2']) === $com2);

        // negPrepend
        assert($com3->negPrepend('noway!') === $com3);

        // neutral
        assert($com3->neutral('User #1',null) === $com3);
        assert($com4->neutral('Row #1',null,null,['neg','noChange'],['neutral','active',null,null,['neg','not a number']],['neutral','name',null,null,['neg','not long enough'],['neg','i dont like']])->isCount(1));
        assert($com4->posPrepend('Your thing went ok')->isCount(2));
        assert($com4->append(null,'noway')->last() === ['neg','noway',null,null]);
        $com9 = clone $com4;
        assert(strlen($com9->neutralPrepend('Row #1',['replace'=>'ok'],'#id',['pos','okidou'])->output($lang)) === 487);
        assert(strlen($com9->neutral('Row #1',['replace2'=>'ok2'],['data-ok'=>true],['pos','END'])->output($lang)) === 536);

        // neutralPrepend
        assert($com3->neutralPrepend('noway!!') === $com3);

        // posNeg
        assert($com->posNeg('login/success','login/alreadyConnected')->isCount(4));
        assert($com->pos('login/success')->isCount(4));
        assert($com->neg('login/success')->isCount(5));

        // posNegPrepend
        assert($com3->posNegPrepend('well','ok')->first() === ['neg','ok',null,null]);

        // posNegLogStrict

        // depth
        assert($com4->depth() === 6);

        // stripFloor
        $com5 = clone $com4;
        assert(strlen($com5->output($lang)) === 439);
        assert($com5->stripFloor() === $com5);
        assert($com5->isCount(3));
        assert(strlen($com5->output($lang)) === 295);

        // keepFloor
        $com5 = clone $com4;
        assert(strlen($com5->keepFloor()->output($lang)) === 144);

        // keepCeiling
        $keep = clone $com4;
        assert(strlen($keep->keepCeiling()->output($lang)) === 149);
        assert(strlen($keep->keepCeiling()->keepCeiling()->output($lang)) === 149);

        // keepFirst
        $keepFirst = clone $com4;
        assert($keepFirst->keepFirst()->output($lang) === "<ul><li class='pos'><span>Your thing went ok</span></li></ul>");

        // keepLast
        $keepLast = clone $com4;
        assert($keepLast->keepLast()->output($lang) === "<ul><li class='neg'><span>noway</span></li></ul>");

        // stripType
        $com5 = clone $com4;
        assert($com5->stripType('neutral')->isCount(2));
        assert($com5->stripType()->isCount(1));

        // keepType
        $com5 = clone $com4;
        assert($com5->keepType('neutral')->isCount(1));

        // prepareIn
        assert(Base\Arrs::is($com5->prepareIn('neutral','pos',['test2'=>['ok','deux']])));

        // prepare

        // output
        assert(strlen($com->output($lang)) === 263);
        assert(!$com->isEmpty());
        assert($com2->output($lang) === "<ul><li class='pos'><span>Ça passe, tout est OK</span></li><li class='neg'><span>Grosse erreur</span></li><li class='neg'><span>Grosse/erreur2</span></li></ul>");
        assert(strlen($com4->output($lang)) === 439);
        assert($comAttr->append('pos','login',null,['data-fake'=>1]) === $comAttr);
        assert($comAttr->output($lang) === "<ul><li class='pos' data-fake='1'><span>login</span></li></ul>");
        assert($comAttr->append('pos','login',null,null,['neg','logout',null,'myclass #id']) === $comAttr);
        assert($comAttr->output($lang) === "<ul><li class='pos' data-fake='1'><span>login</span><ul><li class='neg myclass' id='id'><span>logout</span></li></ul></li></ul>");

        // outputNeg
        assert(strlen($com->outputNeg($lang)) === 220);

        // outputPos
        assert(strlen($com->outputPos($lang)) === 52);

        // outputNeutral
        assert(strlen($com4->outputNeutral($lang)) === 348);

        // makeOutput

        // flush
        assert(strlen($com->flush($lang)) === 263);
        assert($com->isEmpty());

        // flushNeg
        assert($com->neg('login/alreadyConnected'));
        assert(strlen($com->flushNeg($lang,'fr')) === 59);

        // flushPos
        assert($com->pos('login/success'));
        assert(strlen($com->flushPos($lang,'fr')) === 61);
        assert(strlen($com->flush($lang)) === 0);

        // flushNeutral
        assert(strlen($com4->flushNeutral($lang)) === 348);
        assert($com4->flushNeutral($lang) === '');

        // makeFlush

        // error

        // map
        assert(!$com->exists(0));
        assert($com->push(['pos','ok']));
        assert($com->exists(0));

        return true;
    }
}
?>