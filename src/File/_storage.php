<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main\File;
use Quid\Base;
use Quid\Main;

// _storage
// trait that provides methods to link a file object to a specific storage directory
trait _storage
{
    // config
    protected static array $configFileStorage = [
        'dirname'=>null, // dirname par défaut
        'inc'=>0 // chiffre qui s'auto incrémente à chaque ajout d'un élément de cette classe
    ];


    // isStorageDataValid
    // retourne vrai si les datas fournis sont valides
    final public static function isStorageDataValid(...$values):bool
    {
        return true;
    }


    // storageDirname
    // retourne le dirname pour le storage
    final public static function storageDirname():string
    {
        $return = static::$config['dirname'] ?? null;

        if(!empty($return))
        $return = Base\Finder::normalize($return);

        return $return;
    }


    // setStorageDirname
    // permet de changer le dirname pour le storage
    final public static function setStorageDirname(string $value):void
    {
        static::$config['dirname'] = $value;
    }


    // storageFilename
    // retourne le filename pour le log
    // id de réponse ainsi qu'un int qui s'auto-incrémente
    final public static function storageFilename(...$values):string
    {
        return Base\Response::id().'-'.static::$config['inc'];
    }


    // storagePath
    // retourne le chemin à utiliser pour stocker le storage
    final public static function storagePath(...$values):string
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
    final public static function storageData(...$values)
    {
        return (count($values) === 1)? $values[0]:$values;
    }


    // storageAll
    // retourne on objet files avec tous les fichiers dans le dossier de storage
    // l'ordre dans le fichier est alphabétique
    final public static function storageAll(?array $option=null):Main\Files
    {
        $return = Main\Files::newOverload();
        $dirname = static::storageDirname();
        $return->dirVisible($dirname,false,$option);

        return $return;
    }


    // storageSort
    // retourne on objet files avec les fichiers les plus récent dans le dossier de storage
    // les fichiers sont ordonnées du plus récent au plus ancien
    final public static function storageSort($sort=true,?int $limit=null,string $format='dateModify',?array $option=null):Main\Files
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
    final public static function storageSkip(int $limit,$sort=true,string $format='dateModify',?array $option=null):Main\Files
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
    final public static function storageTrim(int $trim):?int
    {
        $unlinks = static::storageSkip($trim,false);
        $return = $unlinks->unlink();

        return $return;
    }


    // storage
    // crée une nouvelle entrée du storage maintenant
    final public static function storage(...$values):?self
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