<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 * Readme: https://github.com/quidphp/main/blob/master/README.md
 */

namespace Quid\Main\Lang;
use Quid\Base;

// en
// english language content used by this namespace
class En extends Base\Lang\En
{
    // trait
    use _overload;


    // config
    public static $config = [

        // error
        'error'=>[

            // label
            'label'=>[
                1=>'Error',
                2=>'Notice',
                3=>'Deprecated',
                11=>'Assertion',
                21=>'Silent',
                22=>'Warning',
                23=>'Fatal',
                31=>'Exception',
                32=>'Catchable exception'
            ]
        ],

        // role
        'role'=>[

            // label
            'label'=>[],

            // description
            'description'=>[]
        ],

        // com
        'com'=>[

            // neg
            'neg'=>[],

            // pos
            'pos'=>[]
        ],

        // relation
        'relation'=>[

            // bool
            'bool'=>[
                0=>'No',
                1=>'Yes'
            ],

            // lang
            'lang'=>[
                'fr'=>'French',
                'en'=>'English'
            ]
        ]
    ];
}

// init
En::__init();
?>