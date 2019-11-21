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

// concatenator
// class for testing Quid\Main\Concatenator
class Concatenator extends Base\Test
{
    // trigger
    final public static function trigger(array $data):bool
    {
        // prepare
        $target = '[assertCurrent]/concatenate.php';
        $_file_ = Base\Finder::normalize('[assertCommon]/class.php');
        $_dir_ = dirname($_file_);

        // construct
        $c = new Main\Concatenator();

        // add
        $option = ['extension'=>'php','priority'=>['class.php']];
        assert($c->add($_dir_,$option) === $c);

        // addStr
        assert($c->addStr('TESTA') === $c);

        // parse
        assert(count($c->parse()) === 2);
        assert(count($c->parse()[0]) === 2);
        assert(strpos($c->parse()[0][0][0],'class.php') !== false);

        // prepareEntry

        // getEntryFiles

        // trigger
        assert(is_string($c->trigger()));

        // triggerWrite
        assert($c->triggerWrite($target) instanceof Main\File);

        // makeEntry

        // prepareEntryFile

        // cleanup
        assert(Base\Dir::empty('[assertCurrent]'));

        return true;
    }
}
?>