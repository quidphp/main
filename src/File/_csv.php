<?php
declare(strict_types=1);
namespace Quid\Main\File;
use Quid\Base;

// _csv
trait _csv
{
	// config
	public static $configFileCsv = [
		'group'=>'csv',
		'option'=>[
			'read'=>[ // option pour read
				'csv'=>true,
				'delimiter'=>';',
				'enclosure'=>'"',
				'escape'=>'\\'],
			'write'=>[ // option pour write
				'csv'=>true,
				'delimiter'=>';',
				'enclosure'=>'"',
				'escape'=>'\\'],
			'toUtf8'=>null, // lance la méthode utf8_encode
			'whiteSpace'=>true, // lance la méthode base/str removeWhiteSpace
			'forbiddenCodePoint'=>true, // lance méthode base/str fixUnicode
			'cast'=>true] // cast les valeurs numérique
	];
	

	// lineReturn
	// gère la valeur de retour pour line
	// peut encoder le retour, trim chaque valeur ainsi que caster les valeurs numériques string
	// méthode protégé
	protected function lineReturn($return) 
	{
		$closure = function(string $return,array $option) {
			if(!empty($option['toUtf8']))
			$return = Base\Encoding::toUtf8($return);
			
			if(!empty($option['whiteSpace']))
			$return = Base\Str::removeWhiteSpace($return);
			
			if(!empty($option['forbiddenCodePoint']))
			$return = Base\Str::fixUnicode($return); 
			
			return $return;
		};
		
		$return = Base\Call::map('string',$closure,$return,$this->option());
		
		$cast = $this->getOption('cast');
		if($cast === true && is_array($return))
		$return = Base\Arrs::cast($return);
		
		return $return;
	}
	
	
	// sourceRewind
	// ramène le pointeur de la source au début
	public function sourceRewind():void
	{
		$this->seekRewind();
		
		return;
	}
	
	
	// sourceOne
	// retourne une entrée de la source
	// i agit comme référence
	public function sourceOne($offset=true,$length=true,int &$i,?array $option=null)
	{
		return $this->lineRef($offset,$length,$i,$option);
	}
	
	
	// targetInsert
	// fait une insertion sur le fichier, utilisé à partir de main/importer
	public function targetInsert(array $data,?array $option=null):bool
	{
		$return = false;
		
		if($this->isWritable())
		{
			$this->seekEnd();
			$this->write($data);
			$return = true;
		}
		
		return $return;
	}
	
	
	// targetUpdate
	// fait une mise à jour sur le fichier, utilisé à partir de main/importer
	public function targetUpdate(array $data,int $offset,?array $option=null):bool
	{
		$return = false;
		
		if($this->isWritable())
		{
			$this->lineSplice($offset,1,$data);
			$return = true;
		}
		
		return $return;
	}
	
	
	// targetDelete
	// fait une suppresion sur le fichier, utilisé à partir de main/importer
	public function targetDelete(int $offset,?array $option=null):bool
	{
		$return = false;
		
		if($this->isWritable())
		{
			$this->lineSplice($offset,1,null);
			$return = true;
		}
		
		return $return;
	}
	
	
	// targetTruncate
	// vide le fichier, utilisé à partir de main/importer
	public function targetTruncate(?array $option=null):bool
	{
		$return = false;
		
		if($this->isWritable())
		{
			$this->empty(0,$option);
			$return = true;
		}
		
		return $return;
	}
}
?>