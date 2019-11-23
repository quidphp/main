<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/core/blob/master/LICENSE
 * Readme: https://github.com/quidphp/core/blob/master/README.md
 */

namespace Quid\Main\Lang;
use Quid\Base;

// fr
// french language content used by this namespace
class Fr extends Base\Lang\Fr
{
    // trait
    use _overload;


    // config
    public static $config = [

        // error
        'error'=>[

            // label
            'label'=>[
                1=>'Erreur',
                2=>'Notification',
                3=>'Déconseillé',
                11=>'Assertion',
                21=>'Erreur silencieuse',
                22=>'Avertissement',
                23=>'Erreur fatale',
                31=>'Exception',
                32=>'Exception attrapable'
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
                0=>'Non',
                1=>'Oui'
            ],

            // lang
            'lang'=>[
                'fr'=>'Français',
                'en'=>'Anglais'
            ]
        ]
    ];
}

// init
Fr::__init();
?>