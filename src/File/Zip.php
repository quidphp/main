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

// zip
// class for a zip file
class Zip extends Binary
{
    // config
    protected static array $config = [
        'group'=>'zip'
    ];


    // dynamique
    protected ?\ZipArchive $archive = null; // garde une copie de l'objet zipArchive


    // archive
    // retourne l'objet ZipArchive
    // créer l'objet si inexistant
    // une exception peut être envoyé si la resource n'est pas un vrai fichier
    final public function archive():\ZipArchive
    {
        if(empty($this->archive))
        {
            $this->check('isFile');

            $path = $this->path();
            $archive = new \ZipArchive();
            $flag = (Base\File::isEmpty($path))? \ZipArchive::OVERWRITE:\ZipArchive::CREATE;

            if($archive->open($path,$flag) !== true)
            static::throw('cannotCreateNewZipArchive');

            $this->archive = $archive;
        }

        return $this->archive;
    }


    // commit
    // commit les changements à l'archive et enlève l'objet archive
    final public function commit():bool
    {
        $return = $this->archive()->close();
        $this->archive = null;

        return $return;
    }


    // all
    // retourne un tableau avec tous les fichiers contenus dans l'archive
    final public function all():array
    {
        $return = [];
        $archive = $this->archive();

        for ($i=0; $i < $archive->count(); $i++)
        {
            $return[$i] = $archive->statIndex($i);
        }

        return $return;
    }


    // addFile
    // ajoute un fichier à l'archive
    // des exceptions peuvent être envoyés
    final public function addFile($value,?string $local=null,?array $option=null):bool
    {
        $return = false;
        $option = Base\Arr::plus(['safeBasename'=>false],$option);
        $archive = $this->archive();
        $this->check('isWritable');

        if($value instanceof Main\File)
        $value = $value->path();

        if(!is_string($value))
        static::throw('invalidValue');

        $value = Base\Finder::normalize($value);

        if(!Base\File::isReadable($value))
        static::throw('fileNotReadable');

        if(!is_string($local))
        $local = Base\Path::basename($value);

        if(is_string($local))
        {
            if($option['safeBasename'] === true)
            $local = Base\Path::safeBasename($local);

            $return = $archive->addFile($value,$local);
        }

        return $return;
    }


    // addFiles
    // permet d'ajouter plusieurs fichiers à l'archive zip
    final public function addFiles($values,?string $local=null,?array $option=null):bool
    {
        $return = false;

        if($values instanceof Main\Files || is_array($values))
        {
            $return = true;

            foreach ($values as $key => $value)
            {
                $return = $this->addFile($value,$local,$option);

                if($return === false)
                break;
            }
        }

        return $return;
    }


    // extract
    // extrait l'archive vers une destination
    final public function extract(string $value):bool
    {
        return $this->archive()->extractTo(Base\Finder::normalize($value));
    }
}

// init
Zip::__init();
?>