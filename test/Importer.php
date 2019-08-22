<?php
declare(strict_types=1);
namespace Quid\Main\Test;
use Quid\Main;
use Quid\Base;

// importer
class Importer extends Base\Test
{
	// trigger
	public static function trigger(array $data):bool
	{
		// prepare
		$source = new class("[assertCommon]/csv.csv",array('toUtf8'=>true)) extends Main\File implements Main\Contract\Import {
			use Main\File\_csv;
			public static $config = array();
			public function __construct(...$args) 
			{
				static::__config();
				parent::__construct(...$args);
			}
		};
		$target = new $source(true);
		$callback = function(array $return) {
			return $return;
		};

		// construct
		$import = new Main\Importer($source,$target,array('truncate'=>true,'callback'=>$callback));

		// setSource

		// source
		assert($import->source() instanceof Main\File);

		// set
		assert($import->set(0,'code')->isCount(1));

		// setTarget

		// target
		assert($import->target() instanceof Main\File);

		// setCallback
		assert($import->setCallback(0,function($v) { return $v; }) === $import);

		// setRequired
		assert($import->setRequired(0,true) === $import);

		// associate
		assert($import->associate(1,'name',false,function(string $v,array $ok) { return $v."ok"; }) === $import);

		// getMap
		assert(count($import->getMap(0)) === 4);
		assert(count($import->getMap(1)) === 4);

		// getMaps
		assert(count($import->getMaps()) === 2);

		// checkMaps
		assert(count($import->checkMaps()) === 2);

		// emulate
		assert($import->emulate(1,10)['total'] === array('valid'=>9,'invalid'=>1,'save'=>0,'noSave'=>10,'insert'=>9,'update'=>0,'delete'=>0));
		assert(count($import->emulate(1,10)['data']) === 10);
		assert(count($import->emulate(1,1)['data']) === 1);
		assert(count($import->emulate(1,0)['data']) === 0);

		// makeTotal

		// one
		assert(count($import->one(array(0=>1234,1=>'OK'))) === 7);
		assert($import->one(array(0=>1234,1=>'OK'))['data'] === array('code'=>1234,'name'=>'OKok'));

		// oneAfter

		// trigger
		assert(count($import->trigger(1,10)) === 2);
		assert(count($target->lines()) === 9);
		assert($target->targetUpdate(array('test'=>999,'james'),1));
		assert($target->targetDelete(4));
		assert(count($target->lines()) === 8);
		
		return true;
	}
}
?>