<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/base/blob/master/LICENSE
 */

namespace Quid\Main\File;
use Quid\Main;
use Quid\Base;

// _session
trait _session
{
	// storageDirname
	// retounre le dirname du storage, le dossier ou les fichiers sessions sont gargés
	public static function storageDirname():string
	{
		return Base\Session::getSavePath(true);
	}


	// sessionSid
	// retourne la clé de session
	public function sessionSid():string
	{
		return $this->filename();
	}


	// sessionData
	// retourne les données de la session
	public function sessionData():string
	{
		return $this->readRaw();
	}


	// sessionWrite
	// écrit de nouvelles données dans le fichier session
	public function sessionWrite(string $data):bool
	{
		$return = true;
		$this->overwrite($data,['callback'=>null]);

		return $return;
	}


	// sessionUpdateTimestamp
	// update le timestamp du fichier session, retourne true même si rien n'a changé
	public function sessionUpdateTimestamp():bool
	{
		$this->touch();

		return true;
	}


	// sessionDestroy
	// détruit le fichier de session
	public function sessionDestroy():bool
	{
		return $this->unlink();
	}


	// sessionDir
	// retourne le directoire de session
	public static function sessionDir(string $path,string $name):string
	{
		$return = null;

		if(!empty($path) && !empty($name))
		$return = Base\Path::append($path,$name);

		return $return;
	}


	// sessionPath
	// retourne le chemin de session
	public static function sessionPath(string $path,string $name,string $sid):string
	{
		$return = null;

		if(!empty($path) && !empty($name) && !empty($sid))
		$return = Base\Path::append($path,$name,$sid);

		return $return;
	}


	// sessionExists
	// retourne vrai si le sid exists pour le nom donné
	public static function sessionExists(string $path,string $name,string $sid):bool
	{
		$return = false;
		$path = static::sessionPath($path,$name,$sid);

		if(Base\File::is($path))
		$return = true;

		return $return;
	}


	// sessionCreate
	// crée une nouvelle session avec le nom et side donné
	// retourne une classe qui implémente Contract\Session
	public static function sessionCreate(string $path,string $name,string $sid):?Main\Contract\Session
	{
		$return = null;

		if(!static::sessionExists($path,$name,$sid))
		{
			$path = static::sessionPath($path,$name,$sid);
			$return = static::new($path,['create'=>true]);
			$return->resource();
		}

		return $return;
	}


	// sessionRead
	// lit une session à partir d'un nom et d'un sid
	// retourne une classe qui implémente Contract\Session
	public static function sessionRead(string $path,string $name,string $sid):?Main\Contract\Session
	{
		$return = null;

		if(static::sessionExists($path,$name,$sid))
		{
			$path = static::sessionPath($path,$name,$sid);
			$return = static::new($path);
		}

		return $return;
	}


	// sessionGarbageCollect
	// lance le processus de garbageCollect pour le nom de session donné
	public static function sessionGarbageCollect(string $path,string $name,int $lifetime,$not=null):int
	{
		$return = 0;
		$dir = static::sessionDir($path,$name);
		$timestamp = Base\Date::timestamp() - $lifetime;
		$files = Base\Dir::getFormatSmaller($dir,$timestamp,'dateAccess');

		if(!is_array($not))
		$not = [$not];

		foreach ($not as $k => $v)
		{
			if($v instanceof Main\Contract\Session)
			$not[$k] = $v->path();

			elseif(!is_string($v))
			unset($not[$k]);
		}

		if(!empty($files))
		{
			$files = Base\Arr::keysStrip($not,$files);
			if(!empty($files))
			{
				$paths = array_keys($files);
				$return = Base\Finder::unlinks(...$paths);
			}
		}

		return $return;
	}
}
?>