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

// xml
// class for testing Quid\Main\Xml
class Xml extends Base\Test
{
    // trigger
    final public static function trigger(array $data):bool
    {
        // construct
        $xml = new Main\Xml('sitemap');

        // toString

        // clone
        $xml2 = clone $xml;
        assert($xml2 !== $xml);

        // cast
        assert(strlen($xml2->_cast()) === 84);

        // xml
        assert($xml->xml() instanceof \SimpleXMLElement);

        // output
        assert(strlen($xml2->output()) === 84);

        // sitemap
        assert($xml->sitemap('https://google.com','test.ok') === $xml);
        assert(strlen($xml->output()) === 161);

        return true;
    }
}
?>