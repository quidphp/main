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
		\assert($arrs->toArray() === ['james'=>['ok'=>2]]);
		unset($arrs['james/ok']);
		\assert($arrs->toArray() === ['james'=>[]]);
		$arrs['james/ok3'] = 3;
		$arrs['james/ok'] = 2;
		$arrs['james/ok4/what'] = 2;
		\assert($arrs->values()[0] === [3,2,[2]]);
		\assert($arrs->exists(['james','ok']));
		\assert(!$arrs->exists(['james','ok2']));
		\assert($arrs->in(2));
		\assert($arrs->search(2) === ['james','ok']);
		\assert($arrs->keys(2) === [['james','ok'],['james','ok4','what']]);
		\assert($arrs->get('james/ok3') === 3);
		\assert($arrs->gets('james/ok3') === ['james/ok3'=>3]);
		\assert($arrs->gets(['james','ok3']) === ['james/ok3'=>3]);
		\assert($arrs->index([0,-1,0]) === 2);
		\assert($arrs->indexes([0,-1,0]) === ['0/-1/0'=>2]);
		\assert(\key($arrs->sort()['james']) === 'ok');
		\assert($arrs->sequential()[0][0] === 2);
		$arrs->empty();
		$arrs['james/ok3'] = 3;
		$arrs['james/ok'] = 2;
		$arrs['james/ok4/what'] = 2;
		\assert($arrs->set('ok/well',3) instanceof Main\Arrs);
		\assert($arrs->set(['ok',null],'z') instanceof Main\Arrs);
		\assert($arrs['ok/0'] === 'z');
		\assert($arrs->sets(['ok/well2'=>true])['ok']['well2'] === true);
		\assert($arrs->unset(['ok','well2'])['ok'] === ['well'=>3,'z']);
		$arrs2 = new Main\Arrs(['james'=>['okz'=>2]]);
		\assert(\count($arrs2->replace($arrs->toArray())['james']) === 4);
		\assert($arrs->remove(2,3,'z')['ok'] === []);
		$arrs3 = new Main\Arrs($arrs2);
		\assert($arrs2->toArray() === $arrs2->toArray());
		$arrs4 = new Main\Arrs(['test/ok'=>2,'james/lol'=>'bla']);
		\assert($arrs4['test']['ok'] === 2);
		\assert($arrs4->replace($arrs,['meh/lol'=>'ok'])['meh']['lol'] === 'ok');
		
		return true;
	}
}
?>