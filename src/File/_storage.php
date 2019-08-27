<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main\File;
use Quid\Main;
use Quid\Base;

// _storage
trait _storage
{
	// config
	public static $configFileStorage = [
		'dirname'=>null, // dirname par défaut
		'inc'=>0 // chiffre qui s'auto incrémente à chaque ajout d'un élément de cette classe
	];


	// isStorageDataValid
	// retourne vrai si les datas fournis sont valides
	public static function isStorageDataValid(...$values):bool
	{
		return true;
	}


	// storageDirname
	// retourne le dirname pour le storage
	public static function storageDirname():string
	{
		$return = static::$config['dirname'] ?? null;

		if(!empty($return))
		$return = Base\Finder::shortcut($return);

		return $return;
	}


	// setStorageDirname
	// permet de changer le dirname pour le storage
	public static function setStorageDirname(string $value):void
	{
		static::$config['dirname'] = $value;

		return;
	}


	// storageFilename
	// retourne le filename pour le log
	// id de réponse ainsi qu'un int qui s'auto-incrémente
	public static function storageFilename(...$values):string
	{
		return Base\Response::id().'-'.static::$config['inc'];
	}


	// storagePath
	// retourne le chemin à utiliser pour stocker le storage
	public static function storagePath(...$values):string
	{
		$return = null;
		$build['dirname'] = static::storageDirname(...$values);
		$build['filename'] = static::storageFilename(...$values);
		$build['extension'] = static::defaultExtension();
		$return = Base\Path::build($build);

		return $return;
	}


	// storageData
	// retourne les données à mettre dans le fichier
	public static function storageData(...$values)
	{
		return (count($values) === 1)? $values[0]:$values;
	}


	// storageAll
	// retourne on objet files avec tous les fichiers dans le dossier de storage
	// l'ordre dans le fichier est alphabétique
	public static function storageAll(?array $option=null):Main\Files
	{
		$return = Main\Files::newOverload();
		$dirname = static::storageDirname();
		$return->dirVisible($dirname,false,$option);

		return $return;
	}


	// storageSort
	// retourne on objet files avec les fichiers les plus récent dans le dossier de storage
	// les fichiers sont ordonnées du plus récent au plus ancien
	public static function storageSort($sort=true,?int $limit=null,string $format='dateModify',?array $option=null):Main\Files
	{
		$return = Main\Files::newOverload();
		$dirname = static::storageDirname();
		$paths = Base\Dir::getFormatSort($dirname,$format,$sort,false,$option);

		if(!empty($paths))
		{
			if(is_int($limit))
			$paths = Base\Arr::unsetAfterCount($limit,$paths);

			$return->add(...array_keys($paths));
		}

		return $return;
	}


	// storageSkip
	// retourne on objet files avec tous les fichiers après la limite spécifié en argument
	// permet par exemple d'effacer tous les fichiers après 100
	public static function storageSkip(int $limit,$sort=true,string $format='dateModify',?array $option=null):Main\Files
	{
		$return = Main\Files::newOverload();
		$dirname = static::storageDirname();
		$paths = Base\Dir::getFormatSortSkip($dirname,$limit,$format,$sort,false,$option);

		if(!empty($paths))
		$return->add(...array_keys($paths));

		return $return;
	}


	// storageTrim
	// trim le nombre d'entré par une valeur paramétré dans static config
	public static function storageTrim(int $trim):?int
	{
		$unlinks = static::storageSkip($trim,false);
		$return = $unlinks->unlink();

		return $return;
	}


	// storage
	// crée une nouvelle entrée du storage maintenant
	// méthode protégé
	public static function storage(...$values):?self
	{
		$return = null;

		if(static::isStorageDataValid(...$values))
		{
			$path = static::storagePath(...$values);
			$data = static::storageData(...$values);
			$file = new static($path,['create'=>true]);

			if(!empty($file))
			{
				$return = $file->write($data);
				static::$config['inc']++;
			}
		}

		else
		static::throw('invalidData');

		return $return;
	}
}
?>