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

// localization
// class for testing Quid\Main\Localization
class Localization extends Base\Test
{
    // trigger
    final public static function trigger(array $data):bool
    {
        // construct
        $l = new Main\Localization(['lat'=>2.2,'lng'=>2.1,'countryCode'=>'US','input'=>'test']);
        $l2 = clone $l;

        // toString

        // onPrepareReplace

        // cast
        assert($l->_cast() === $l->toArray());

        // inUsa
        assert($l->inUsa());

        // lat
        assert($l->lat() === 2.2);

        // lng
        assert($l->lng() === 2.1);

        // latLng
        assert($l->latLng() === ['lat'=>2.2,'lng'=>2.1]);

        // input
        assert($l->input() === 'test');

        // countryCode
        assert($l->countryCode() === 'US');

        return true;
    }
}
?>