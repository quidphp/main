<?php
declare(strict_types=1);
namespace Quid\Main\Test;
use Quid\Main;
use Quid\Base;

// arrs
class Arrs extends Base\Test
{
	// trigger
	public static function trigger(array $data):bool
	{
		// construct
		$arrs = new Main\Arrs();
		
		// map
		$arrs['james/ok'] = 2;
		assert($arrs->toArray() === array('james'=>array('ok'=>2)));
		unset($arrs['james/ok']);
		assert($arrs->toArray() === array('james'=>array()));
		$arrs['james/ok3'] = 3;
		$arrs['james/ok'] = 2;
		$arrs['james/ok4/what'] = 2;
		assert($arrs->values()[0] === array(3,2,array(2)));
		assert($arrs->exists(array('james','ok')));
		assert(!$arrs->exists(array('james','ok2')));
		assert($arrs->in(2));
		assert($arrs->search(2) === array('james','ok'));
		assert($arrs->keys(2) === array(array('james','ok'),array('james','ok4','what')));
		assert($arrs->get('james/ok3') === 3);
		assert($arrs->gets('james/ok3') === array('james/ok3'=>3));
		assert($arrs->gets(array('james','ok3')) === array('james/ok3'=>3));
		assert($arrs->index(array(0,-1,0)) === 2);
		assert($arrs->indexes(array(0,-1,0)) === array('0/-1/0'=>2));
		assert(key($arrs->sort()['james']) === 'ok');
		assert($arrs->sequential()[0][0] === 2);
		$arrs->empty();
		$arrs['james/ok3'] = 3;
		$arrs['james/ok'] = 2;
		$arrs['james/ok4/what'] = 2;
		assert($arrs->set('ok/well',3) instanceof Main\Arrs);
		assert($arrs->set(array('ok',null),'z') instanceof Main\Arrs);
		assert($arrs['ok/0'] === 'z');
		assert($arrs->sets(array('ok/well2'=>true))['ok']['well2'] === true);
		assert($arrs->unset(array('ok','well2'))['ok'] === array('well'=>3,'z'));
		$arrs2 = new Main\Arrs(array('james'=>array('okz'=>2)));
		assert(count($arrs2->replace($arrs->toArray())['james']) === 4);
		assert($arrs->remove(2,3,'z')['ok'] === array());
		$arrs3 = new Main\Arrs($arrs2);
		assert($arrs2->toArray() === $arrs2->toArray());
		$arrs4 = new Main\Arrs(array('test/ok'=>2,'james/lol'=>'bla'));
		assert($arrs4['test']['ok'] === 2);
		assert($arrs4->replace($arrs,array('meh/lol'=>'ok'))['meh']['lol'] === 'ok');
		
		return true;
	}
}
?>