<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Test\Main;
use Quid\Base;
use Quid\Main;

// insensitive
// class for testing Quid\Main\Insensitive
class Insensitive extends Base\Test
{
    // trigger
    public static function trigger(array $data):bool
    {
        // construct
        $ins = new Main\Insensitive(['test'=>'James','ok'=>2]);
        $vins = new Main\Insensitive(['test','TEST','OK']);
        $ins2 = new Main\Insensitive();

        // map
        $ins2['TEST'] = 'OK';
        assert($ins2['test'] === 'OK');
        unset($ins2['tEST']);
        $ins2['tést'] = 'OKé';
        assert($ins2['TÉST'] === 'OKé');
        assert($ins->append(['test'=>'meh'],['TEST'=>'LOL'])->toArray() === ['ok'=>2,'TEST'=>'LOL']);
        assert($ins->prepend(22,33,'testz')[0] === 22);
        assert($ins->append(22,33,'testz')[5] === 'testz');
        assert($ins->remove(22,33,'testz') === $ins);
        assert($ins->count() === 2);
        assert($ins->exists('test'));
        assert($ins->exists('TEST'));
        assert($ins2->in('OKÉ'));
        assert($ins2->search('OKÉ') === 'tést');
        assert($vins->count() === 3);
        assert($vins->keys('test') === [0,1]);
        assert($ins2->gets('TÉST') === ['TÉST'=>'OKé']);
        assert($ins2->slice('TÉST',true) === ['tést'=>'OKé']);
        assert($ins->set('TeSt',2)->count(2));
        assert($ins->get('test') === 2);
        assert($ins->unset('TeST')->count() === 1);
        $ins2['ok'] = 2;

        return true;
    }
}
?>