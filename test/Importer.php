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

// importer
// class for testing Quid\Main\Importer
class Importer extends Base\Test
{
    // trigger
    final public static function trigger(array $data):bool
    {
        // prepare
        $source = Main\File::new('[assertCommon]/csv.csv',['toUtf8'=>true]);
        $target = new $source(true);
        $callback = fn(array $return) => $return;

        // construct
        $import = new Main\Importer($source,$target,['truncate'=>true,'callback'=>$callback]);

        // setSource

        // source
        assert($import->source() instanceof Main\File);

        // set
        assert($import->set(0,'code')->isCount(1));

        // setTarget

        // target
        assert($import->target() instanceof Main\File);

        // setCallback
        assert($import->setCallback(0,fn($v) => $v) === $import);

        // setRequired
        assert($import->setRequired(0,true) === $import);

        // associate
        assert($import->associate(1,'name',false,fn(string $v,array $ok) => $v.'ok') === $import);

        // getMap
        assert(count($import->getMap(0)) === 4);
        assert(count($import->getMap(1)) === 4);

        // getMaps
        assert(count($import->getMaps()) === 2);

        // checkMaps
        assert(count($import->checkMaps()) === 2);

        // emulate
        assert($import->emulate(1,10)['total'] === ['valid'=>9,'invalid'=>1,'save'=>0,'noSave'=>10,'insert'=>9,'update'=>0,'delete'=>0]);
        assert(count($import->emulate(1,10)['data']) === 10);
        assert(count($import->emulate(1,1)['data']) === 1);
        assert(count($import->emulate(1,0)['data']) === 0);

        // makeTotal

        // one
        assert(count($import->one([0=>1234,1=>'OK'])) === 7);
        assert($import->one([0=>1234,1=>'OK'])['data'] === ['code'=>1234,'name'=>'OKok']);

        // oneAfter

        // trigger
        assert(count($import->trigger(1,10)) === 2);
        assert(count($target->lines()) === 9);
        assert($target->targetUpdate(['test'=>999,'james'],1));
        assert($target->targetDelete(4));
        assert(count($target->lines()) === 8);

        return true;
    }
}
?>