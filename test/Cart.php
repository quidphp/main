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

// cart
// class for testing Quid\Main\Cart
class Cart extends Base\Test
{
    // trigger
    final public static function trigger(array $data):bool
    {
        // construct
        $cart = new Main\Cart([['test',2]]);

        // onPrepareValue

        // getItem

        // add
        $cart->add('test',1,['special'=>true]);
        $cart->add('test2',3,['special'=>true]);
        assert($cart->isCount(3));

        // update
        $cart->update(0,3);
        assert($cart->get(0)['quantity'] === 3);

        // updateQuantity

        // updateRelative
        $cart->updateRelative(0,-2);
        assert($cart[0]['quantity'] === 1);
        $cart->updateRelative(0,-2);
        assert($cart->isCount(2));

        // commit

        // indexesFromItem
        $cart->add('test',1,['special'=>true]);
        assert($cart->indexesFromItem('test') === [1,3]);
        assert($cart->indexesFromItem('testz') === []);

        // map
        $cart->filter(function(array $array) {
            ['item'=>$item] = $array;
            return $item === 'test2';
        });
        assert($cart->isCount(1));
        assert($cart->isNotEmpty());
        $cart->unset(2);
        assert($cart->isEmpty());

        return true;
    }
}
?>