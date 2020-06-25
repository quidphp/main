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

// cart
// class for testing Quid\Main\Cart
class Cart extends Base\Test
{
    // trigger
    final public static function trigger(array $data):bool
    {
        // construct
        $cart = new Main\Cart([['test',2]]);
        $cart2 = new Main\Cart([['test',2],['test2',3]]);

        // onPrepareValue

        // hasItem
        assert($cart->hasItem('test'));
        assert(!$cart->hasItem('test2'));

        // getIndexFromItem
        assert($cart->getIndexFromItem('test') === 0);
        assert($cart->getIndexFromItem('test2') === null);

        // getIndexesFromItem
        assert($cart->getIndexesFromItem('testz') === []);

        // getItemFromIndex
        assert($cart->getItemFromIndex(0) === 'test');
        assert($cart->getItemFromIndex(1) === null);

        // getQuantityFromIndex
        assert($cart->getQuantityFromIndex(0) === 2);
        assert($cart->getQuantityFromIndex(1) === null);

        // getQuantityFromItem
        assert($cart->getQuantityFromItem('test') === 2);
        assert($cart->getQuantityFromItem('test2') === null);

        // add
        $cart->add('test',1,['special'=>true]);
        $cart->add('test2',3,['special'=>true]);
        assert($cart->isCount(3));

        // update
        $cart2->update('test2',4);
        assert($cart2->get(1)['quantity'] === 4);

        // updateQuantity
        $cart2->updateQuantity('test2',5);
        assert($cart2->get(1)['quantity'] === 5);

        // updateRelative
        $cart2->updateRelative('test2',-1);
        assert($cart2->get(1)['quantity'] === 4);

        // addOrUpdate

        // updateIndex
        $cart->updateIndex(0,3);
        assert($cart->get(0)['quantity'] === 3);

        // updateIndexQuantity
        $cart->updateIndexQuantity(0,3);
        assert($cart->get(0)['quantity'] === 3);

        // updateIndexRelative
        $cart->updateIndexRelative(0,-2);
        assert($cart[0]['quantity'] === 1);
        $cart->updateIndexRelative(0,-2);
        assert($cart->isCount(2));

        // commit

        // map
        $cart->add('test',1,['special'=>true]);
        assert($cart->getIndexesFromItem('test') === [1,3]);
        $cart->filter(fn(array $array) => $array['item'] === 'test2');
        assert($cart->isCount(1));
        assert($cart->isNotEmpty());
        $cart->unset(2);
        assert($cart->isEmpty());

        return true;
    }
}
?>