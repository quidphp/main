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

// responseCurrent
// class for testing Quid\Main\ResponseCurrent
class ResponseCurrent extends Base\Test
{
    // trigger
    final public static function trigger(array $data):bool
    {
        // prepare
        $response = Main\ResponseCurrent::singleton();

        // arrObj
        if(!Base\Server::isCli())
        {
            assert(isset($response['quid-Version']));
            assert(is_string($response['quid-Version']));
            $old = $response['quid-Version'];
            $response['quid-Version'] = 'james';
            assert($response['quid-Version'] === 'james');
            unset($response['qUid-Version']);
            assert(!isset($response['quid-Version']));
            $response['Quid-Version'] = $old;
        }

        // call
        assert(is_array($response->headers()));
        if(!Base\Server::isCli())
        assert(is_string($response->getHeader('Quid-Version')));

        return true;
    }
}
?>