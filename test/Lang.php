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

// lang
// class for testing Quid\Main\Lang
class Lang extends Base\Test
{
    // trigger
    final public static function trigger(array $data):bool
    {
        // prepare
        $boot = $data['boot'];
        $fr = $boot->getAttr('assert/lang/fr');
        $en = $boot->getAttr('assert/lang/en');
        $frFile = $boot->getAttr('assert/langFile/fr');

        // construct
        $lang = new Main\Lang(['fr','en','de'],['onLoad'=>function(string $value) { }]);
        assert($lang->overwrite($fr::$config)->isNotEmpty());
        $lang['test'] = ['ok'=>['test'=>'what']];

        // invoke
        assert(count($lang('error','label')) === 13);

        // onPrepareSetInst

        // onSetInst

        // onPrepareUnsetInst

        // onUnsetInst

        // onChange

        // onPrepareReplace

        // baseCall
        assert(count($lang->baseCall('errorCode')) === 16);

        // arr

        // load
        assert($lang->load('de') === $lang);

        // isLang
        assert($lang->isLang('en'));

        // checkLang

        // isLangLoaded
        assert($lang->isLangLoaded('fr'));

        // isCurrent
        assert($lang->isCurrent('fr'));
        assert($lang->changeLang('en'));
        assert(!$lang->isCurrent('za'));

        // isOther
        assert($lang->isOther('de'));
        assert(!$lang->isOther('en'));

        // currentLang
        assert($lang->currentLang() === 'en');

        // defaultLang
        assert($lang->defaultLang() === 'fr');

        // otherLang
        assert($lang->otherLang() === 'fr');
        assert($lang->otherLang(0,'fr') === 'en');
        assert($lang->otherLang(true,'fr') === 'en');
        assert($lang->otherLang('en','fr') === 'en');

        // othersLang
        assert($lang->othersLang() === ['fr','de']);
        assert($lang->othersLang('fr') === ['en','de']);

        // allLang
        assert($lang->allLang() === ['fr','en','de']);

        // countLang
        assert($lang->countLang() === 3);

        // codeLang
        assert($lang->codeLang('baaa') === 'en');
        assert($lang->codeLang('da') === 'da');

        // setLang
        assert($lang->setLang('fr',['fr','en','de','ge']) instanceof Main\Lang);
        assert($lang->countLang() === 4);

        // addLang
        assert($lang->addLang('bl','ok')->countLang() === 6);

        // removeLang
        assert($lang->removeLang('bl','ok')->countLang() === 4);

        // changeLang
        assert($lang->changeLang('en')->currentLang() === 'en');
        assert($lang->removeLang('de','ge')->countLang() === 2);

        // getCallable
        assert($lang->getCallable()[0] === $lang);

        // checkInst

        // take
        assert($lang->isEmpty());
        assert($lang->changeLang('en')->overwrite($en::$config)->changeLang('fr')->changeLang('fr')->overwrite($fr::$config)->isNotEmpty());
        assert(!$lang->isEmpty());
        assert($lang->isNotEmpty());
        $lang->replace(['lol'=>['lol2'=>false,'lol1'=>1,'lol3'=>'oui','lol4'=>'non','lol5'=>[1,'ok'=>'okfr'],'fr'=>'yepfr','replace'=>'Mon%l% [remplacement]']]);
        $lang->changeLang('en')->replace(['lol'=>['lol2'=>true,'lol1'=>2,'lol3'=>'yes','lol4'=>'no','lol5'=>[1,'ok'=>'oken'],'en'=>'yepen','replace'=>'My%l% [replace]']])->changeLang('fr');
        assert($lang->take('number/sizeFormat/text','en')[0] === 'Byte');
        assert($lang->take('number/sizeFormat/text','fr')[0] === 'Octet');
        assert(count($lang->take('number/format')) === 3);
        assert($lang->replace($frFile) === $lang);

        // existsAppend
        assert($lang->existsAppend('lol','lol5','ok'));
        assert(!$lang->existsAppend('lol','lol5','okz'));

        // existsTake
        assert($lang->existsTake('lol/fr'));
        assert(!$lang->existsTake('lol/en'));
        assert($lang->existsTake('lol/en','en'));

        // existsText
        assert(!$lang->existsText('lol/lol5'));
        assert($lang->existsText('lol/fr'));
        assert(!$lang->existsText('lol/en'));
        assert($lang->existsText('lol/en','en'));

        // existsTextAppend
        assert($lang->existsTextAppend('lol','lol5','ok'));
        assert(!$lang->existsTextAppend('lol','lol5'));
        assert($lang->existsTextAppend('lol','fr'));
        assert(!$lang->existsTextAppend('lol','en'));

        // takes
        assert($lang->takes([['lol/lol2'],'lol/lol1']) === ['lol/lol2'=>false,'lol/lol1'=>1]);
        assert($lang->takes([['lol/lol2'],'lol/lol1'],'en') === ['lol/lol2'=>true,'lol/lol1'=>2]);

        // takeUnpack
        assert($lang->takeUnpack(['lol/lol1','fr']) === 1);
        assert($lang->takeUnpack(['lol/lol1','en']) === 2);

        // getAppend
        assert($lang->getAppend('lol','lol1') === 1);

        // getAll
        assert($lang->getAll('lol/lol1') === ['fr'=>1,'en'=>2]);

        // getOthers
        assert($lang->getOthers('lol/lol1') === ['en'=>2]);

        // text
        assert($lang->text('lol/lol1') === '1');
        assert($lang->text('lol/lol1',null,'en') === '2');
        assert($lang->text('lol/replace',['remplacement'=>'OK']) === 'Mon%l% OK');
        assert($lang->text('lc|lol/replace') === 'mon%l% [remplacement]');
        assert($lang->text('lc|lol/replace',null,null,['pattern'=>3]) === 'mon');
        assert($lang->text('lc|lol/replace',null,null,['pattern'=>4]) === 'm...');
        assert($lang->text(['lol','replace'],null,null,['case'=>'lc']) === 'mon%l% [remplacement]');

        // textAfter
        assert($lang->textAfter('ok',['pattern'=>'%lol','html'=>'div']) === '<div>ok</div>lol');
        assert($lang->textAfter('ok') === 'ok');

        // textOption
        assert(count($lang->attr()) === 7);
        assert(count($lang->textOption(['error'=>false])) === 9);
        assert($lang->textOption(['error'=>false])['error'] === false);

        // textReplace

        // textNotFound

        // textAppendOne
        assert($lang->textAppendOne('lol','lol3') === 'oui');
        assert($lang->textAppendOne(['lol'],'lol5/ok') === 'okfr');
        assert($lang->textAppendOne('tc|lol','lol3') === 'Oui');

        // textAppend
        assert($lang->textAppend('lol','lol5','ok') === 'okfr');
        assert($lang->textAppend('lol',['lol5','ok']) === 'okfr');

        // textUnpack
        assert($lang->textUnpack(['lol/lol5/ok',null,'en']) === 'oken');
        assert($lang->textUnpack(['lol/replace',['remplacement'=>'OK']]) === 'Mon%l% OK');
        assert($lang->textUnpack(['lol/replace',['replace'=>'OK'],'en']) === 'My%l% OK');

        // textAll
        assert($lang->textAll('lol/replace') === ['fr'=>'Mon%l% [remplacement]','en'=>'My%l% [replace]']);
        assert($lang->textAll('lol/replace',['remplacement'=>'OK','replace'=>'OK']) === ['fr'=>'Mon%l% OK','en'=>'My%l% OK']);

        // textOthers
        assert($lang->textOthers('lol/replace') === ['en'=>'My%l% [replace]']);
        assert($lang->textOthers('lol/replace',['replace'=>'OK']) === ['en'=>'My%l% OK']);

        // plural
        assert($lang->plural(2,'lol/replace') === 'Mon%l% [remplacement]s');
        assert($lang->plural(1,'lol/replace') === 'Mon%l% [remplacement]');
        assert($lang->plural([2,3,4],'lol/replace',null,['l'=>'w']) === 'Monw [remplacement]');
        assert($lang->plural(1,'lol/replace',null,['l'=>'YYY']) === 'Monl [remplacement]');
        assert($lang->plural(2,'lol/replace',null,['l'=>'YYY'],'en') === 'MyYYY [replace]');
        assert($lang->plural(1,'lol/replace',null,['l'=>'YYY'],'en') === 'Myl [replace]');
        assert($lang->plural(2,'lcf|lol/replace',null,['l'=>'YYY'],'en') === 'myYYY [replace]');
        assert($lang->plural(2,'lc|lol/replace',null,['l'=>'YYY'],'en') === 'myYYY [replace]'); // le remplacement vient avant plural
        assert($lang->plural(2,'plural/1') === 'tests oks');
        assert($lang->plural(2,'plural/2') === 'testas');

        // html
        assert($lang->html('div','lol/lol3') === '<div>oui</div>');
        assert($lang->html(['div',['#myId','element']],'lol/lol3') === "<div id='myId' class='element'>oui</div>");
        assert($lang->html(['div',['#myId','element']],'lol/lol3',null,'en') === "<div id='myId' class='element'>yes</div>");
        assert($lang->html(['div',['#myId','element']],'ucf|lol/lol3',null,'en') === "<div id='myId' class='element'>Yes</div>");

        // pattern
        assert($lang->pattern('%:','lol/lol3') === 'oui:');
        assert($lang->pattern('','lol/lol3') === '');
        assert($lang->pattern('a','lol/lol3') === 'a');
        assert($lang->pattern(':%:','uc|lol/lol3') === ':OUI:');
        assert($lang->pattern(2,'lol/lol3') === 'ou');

        // strict
        assert($lang->strict('lol/lol3') === 'oui');

        // safe
        assert($lang->safe('lol/lol3') === 'oui');
        assert($lang->safe('lol/lol34') === null);

        // alt
        assert($lang->alt('ok/asds/lol','lol/lol3') === 'oui');

        // other
        assert($lang->other('lol/fr') === 'yepfr');
        assert($lang->other('lol/en') === 'yepen');
        assert($lang->other('lol/en','en') === 'yepen');

        // def
        assert($lang->def('lol/lol3') === 'oui');
        assert($lang->def('ok/asds/lol') === '[ok/asds/lol]');

        // same
        assert($lang->same('ok/asds/lol') === 'ok/asds/lol');

        // translate
        assert($lang->translate('non') === ['lol/lol4'=>'no']);
        assert($lang->translate('no','fr','en') === ['lol/lol4'=>'non']);

        // numberFormat
        assert(count($lang->numberFormat()) === 3);
        assert($lang->numberFormat('thousand') === ' ');

        // numberPercentFormat
        assert(count($lang->numberPercentFormat()) === 4);

        // numberMoneyFormat
        assert(count($lang->numberMoneyFormat()) === 4);
        assert($lang->numberMoneyFormat('decimal') === 2);

        // numberPhoneFormat
        assert(count($lang->numberPhoneFormat()) === 2);

        // numberSizeFormat
        assert(count($lang->numberSizeFormat()) === 2);
        assert($lang->numberSizeFormat('text/0') === 'Octet');

        // dateMonth
        assert(count($lang->dateMonth()) === 12);
        assert($lang->dateMonth(1) === 'Janvier');

        // dateFormat
        assert(count($lang->dateFormat()) === 11);
        assert($lang->dateFormat(0) === 'j %n% Y');

        // dateStr
        assert(count($lang->dateStr()) === 7);
        assert($lang->dateStr('year') === 'année');

        // datePlaceholder
        assert(count($lang->datePlaceholder()) === 3);
        assert($lang->datePlaceholder('dateToDay','en') === 'MM-DD-YYYY');
        assert($lang->datePlaceholder('dateToDay') === 'JJ-MM-AAAA');
        
        // dateDay
        assert($lang->dateDay()[0] === 'Dimanche');

        // dateDayShort
        assert(count($lang->dateDayShort()) === 7);

        // headerResponseStatus
        assert(count($lang->headerResponseStatus()) === 61);
        assert($lang->headerResponseStatus(302) === 'Trouvé');

        // errorCode
        assert(count($lang->errorCode()) === 16);
        assert($lang->errorCode(E_RECOVERABLE_ERROR) === 'E_RECOVERABLE_ERROR');

        // errorLabel
        assert(count($lang->errorLabel()) === 13);
        assert($lang->errorLabel(1) === 'Erreur');
        assert($lang->errorLabel(1,'en') === 'Error');

        // existsCom
        assert($lang->existsCom('pos','insert/*/success'));

        // com
        assert($lang->com('neg','login/cantFindUser') === "L'utilisateur n'existe pas");
        assert($lang->com('neg','login/cantFindUserz') === null);
        assert($lang->com('pos','insert/*/success') === 'Ajout effectué');

        // pos
        assert($lang->pos('login/success') === 'Connexion réussie');
        assert($lang->pos('login/cantFindUserz') === '[com/pos/login/cantFindUserz]');

        // neg
        assert($lang->neg('login/userCantLogin') === "L'utilisateur ne peut pas se connecter");
        assert($lang->neg('login/cantFindUserz') === '[com/neg/login/cantFindUserz]');

        // existsRelation
        assert($lang->existsRelation('bool/1'));
        assert(!$lang->existsRelation('bool/appaa'));

        // relation
        assert(Base\Arrs::is($lang->relation()));
        assert($lang->relation('bool/1','en') === 'Yes');

        // bool
        assert($lang->bool(true) === 'Oui');
        assert($lang->bool(false,'en') === 'No');
        assert($lang->bool(0,'en') === 'No');
        assert($lang->bool('0','en') === 'No');

        // langLabel
        assert($lang->langLabel('fr') === 'Français');

        // roleLabel
        assert($lang->roleLabel(80) === 'Administrateur');
        assert($lang->roleLabel(80,'en') === 'Admin');

        // roleDescription
        assert($lang->roleDescription(3) === null);

        // getPath
        assert($lang->getPath('numberFormat') === 'number/format');

        // inst

        // ArrObj
        $lang->unset('lol')->changeLang('en')->unset('lol')->changeLang('fr');
        assert($lang->set('lol/lol2/ok','value') instanceof Main\Lang);
        assert($lang->sets(['lol/lol2/ok2'=>'value2','lol/lol2/ok3'=>'value3']) instanceof Main\Lang);
        $lang['test'] = 2;
        assert($lang['test'] === 2);
        unset($lang['test']);
        assert($lang['lol/lol2/ok'] === 'value');
        $lang['lol/lol2/ok1000'] = 'what';
        assert($lang['lol/lol2/ok1000'] === 'what');
        unset($lang['lol/lol2/ok1000']);
        $count = count($lang);
        $i = 0;
        foreach ($lang as $key => $value)
        {
            assert(is_scalar($key));
            assert(!empty($value));
            $i++;
        }
        assert($i === $count);

        // map
        assert($lang->index([-1,-1,-1]) === 'value3');
        assert($lang->set('lol/lol2/ok','value') instanceof Main\Lang);
        assert($lang->sets(['lol/lol2/ok2'=>'value2','lol/lol2/ok3'=>'value3']) instanceof Main\Lang);
        assert($lang->exists('lol/lol2/ok','lol/lol2/ok2'));
        assert(!$lang->exists('lol/lol2/ok','lol/lol2/ok4'));
        assert($lang->gets('lol/lol2/ok',['lol','lol2','ok2']) === ['lol/lol2/ok'=>'value','lol/lol2/ok2'=>'value2']);
        assert(Base\Arr::isMulti($lang->values()));
        assert($lang->in('value3'));
        assert($lang->inFirst('value4','value3') === 'value3');
        assert($lang->existsFirst('lol2',['lol','lol2','ok']) === ['lol','lol2','ok']);
        assert(count($lang->slice('lol',true)) === 1);
        assert($lang->replace(['lol'=>['final'=>true]])['lol']['final'] === true);
        assert($lang->get($lang->keys('value3')[0]) === 'value3');
        assert($lang->get($lang->search('value3')) === 'value3');
        assert(count($lang->unset('lol/lol2/ok')->get('lol/lol2')) === 2);
        assert($lang->empty()->isEmpty());
        assert($lang->overwrite($fr::$config)->isNotEmpty());
        assert($lang->count() > 2);
        assert(Base\Arr::isMulti($lang->keys()));
        assert($lang->set(['lol',null,2,null],'new')->get('lol/0/2/0') === 'new');
        assert($lang->isSensitive());
        assert(count($lang->remove('Janvier')['date']['month']) === 11);

        return true;
    }
}
?>