<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
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
    protected static array $config = [

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