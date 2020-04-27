<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 * Readme: https://github.com/quidphp/main/blob/master/README.md
 */

namespace Quid\Main\File;

// xml
// class for an xml file
class Xml extends Text
{
    // config
    protected static array $config = [
        'group'=>'xml'
    ];
}

// init
Xml::__init();
?>