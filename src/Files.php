<?php
declare(strict_types=1);
namespace Quid\Main;
use Quid\Base;

// files
class Files extends Map
{
	// trait
	use Map\_obj, Map\_sequential, Map\_count, Map\_sort;
	
	
	// config
	public static $config = [];
	
	
	// map
	protected static $allow = ['add','set','unset','remove','empty','unsetAfterCount','filter','sort','sequential','clone']; // méthodes permises
	
	
	// construct
	// construit un nouvel objet files
	public function __construct(...$values) 
	{
		if(!empty($values))
		$this->add(...$values);
		
		return;
	}
	
	
	// onPrepareReturns
	// prépare le retour pour indexes, gets, slice et slice index
	// les lignes sont toujours retournés dans un nouvel objet cells
	protected function onPrepareReturns(array $array):self
	{
		$return = new static();
		
		foreach ($array as $key => $value) 
		{
			if(!empty($value))
			$return->set($key,$value);
		}
		
		return $return;
	}
	
	
	// safeBasename
	// retourne tous les basenames des fichiers
	public function safeBasename(?string $key=null):?array
	{
		return $this->pair('safeBasename',$key);
	}
	
	
	// set
	// ajoute un fichier dans l'objet files
	public function set($key,$value):parent 
	{
		if(!$value instanceof File)
		$value = File::newOverload($value);
		
		if($value instanceof File)
		parent::set($key,$value);
		
		else
		static::throw('invalidFile');
		
		return $this;
	}
	
	
	// add
	// permet d'ajouter plusieurs fichiers dans files
	// possible aussi de fournir un autre objet files
	public function add(...$values):self 
	{
		$this->checkAllowed('add');
		$values = $this->prepareValues(...$values);
		
		foreach ($values as $value)
		{
			if(!empty($value))
			$this->set(null,$value);
		}
		
		return $this;
	}
	
	
	// dirMethod
	// ajoute le contenu de tout un directoire dans l'objet, la méthode à utiliser doit être fourni en premier argument
	// peut envoyer une exception
	public function dirMethod(string $method,...$args):self
	{
		if(\strpos($method,'get') === 0)
		{
			$files = Base\Dir::$method(...$args);
			
			if(\is_array($files))
			{
				foreach ($files as $v) 
				{
					if(!\is_dir($v))
					$this->set(null,$v);
				}
			}
		}
		
		else
		static::throw('invalidMethod',$method);
		
		return $this;
	}
	
	
	// dir
	// ajoute le contenu de tout un directoire dans l'objet
	// utilise la méthode dir::get
	public function dir($path,bool $dig=false,?array $option=null):self 
	{
		return $this->dirMethod('get',$path,$dig,$option);
	}

	
	// dirVisible
	// ajoute tous les fichiers visibles du répertoire dans l'objet
	// utilise la méthode dir::getVisible
	public function dirVisible($path,bool $dig=false,?array $option=null):self 
	{
		return $this->dirMethod('getVisible',$path,$dig,$option);
	}
	
	
	// dirExtension
	// ajoute le contenu de tout un directoire dans l'objet dont l'extension est celle fourni
	// utilise la méthode dir::getExtension
	public function dirExtension($path,$extension=null,bool $dig=false,?array $option=null):self 
	{
		return $this->dirMethod('getExtension',$path,$extension,$dig,$option);
	}
	
	
	// concatenate
	// permet de concatener tous les fichiers et retourner le rendu dans un fichier
	public function concatenate($target,?callable $callable=null,string $separator=PHP_EOL,?array $option=null):File
	{
		$return = File::newCreate($target,$option);
		Base\Res::concatenate($return->resource(),$callable,$separator,...$this->pair('resource'));
		return $return;
	}
	
	
	// concatenateString
	// permet de concatener tous les fichiers et retourner le rendu combiné dans une string
	public function concatenateString(?callable $callable=null,string $separator=PHP_EOL):?string
	{
		return Base\Res::concatenateString($callable,$separator,...$this->pair('resource'));
	}
	
	
	// unlink
	// efface tous les fichiers de l'objet
	// retourne le nombre de fichier effacés
	public function unlink():int 
	{
		$return = 0;
		
		foreach ($this->arr() as $file) 
		{
			if($file->unlink() === true)
			$return++;
		}
		
		return $return;
	}
}
?>