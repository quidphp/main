<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main\File;

// xml
// class for an xml file
class Xml extends Text
{
    // config
    public static $config = [
        'group'=>'xml'
    ];
}

// init
Xml::__init();
?>