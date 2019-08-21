<?php
declare(strict_types=1);
namespace Quid\Main\Test;
use Quid\Main;
use Quid\Base;

// concatenator
class Concatenator extends Base\Test
{
	// trigger
	public static function trigger(array $data):bool
	{
		// prepare
		$target = "[assertCurrent]/concatenate.php";
		$_file_ = Base\Finder::shortcut("[assertCommon]/class.php");
		$_dir_ = \dirname($_file_);
		
		// construct
		$c = new Main\Concatenator();

		// add
		$option = ['extension'=>'php','priority'=>['class.php']];
		\assert($c->add($_dir_,$option) === $c);
		
		// addStr
		\assert($c->addStr('TESTA') === $c);
		
		// parse
		\assert(\count($c->parse()) === 2);
		\assert(\count($c->parse()[0]) === 2);
		\assert(\strpos($c->parse()[0][0][0],'class.php') !== false);

		// prepareEntry

		// getEntryFiles

		// trigger
		\assert(\is_string($c->trigger()));
		
		// triggerWrite
		\assert($c->triggerWrite($target) instanceof Main\File);
		
		// makeEntry

		// prepareEntryFile

		// cleanup
		\assert(Base\Dir::empty("[assertCurrent]"));
		
		return true;
	}
}
?>