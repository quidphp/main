<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main;
use Quid\Base;

// files
// class for a collection containing many file objects
class Files extends MapObj
{
    // trait
    use Map\_sequential;
    use Map\_count;
    use Map\_sort;


    // config
    protected static array $config = [];


    // dynamique
    protected ?array $mapAllow = ['add','set','unset','remove','empty','unsetAfterCount','filter','sort','sequential','clone']; // méthodes permises


    // construct
    // construit un nouvel objet files
    final public function __construct(...$values)
    {
        if(!empty($values))
        $this->add(...$values);
    }


    // onPrepareReturns
    // prépare le retour pour indexes, gets, slice et slice index
    // les lignes sont toujours retournés dans un nouvel objet cells
    final protected function onPrepareReturns(array $array):self
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
    final public function safeBasename(?string $key=null):?array
    {
        return $this->pair('safeBasename',$key);
    }


    // set
    // ajoute un fichier dans l'objet files
    final public function set($key,$value,?array $option=null):self
    {
        $this->checkAllowed('set');

        if(!$value instanceof File)
        $value = File::newOverload($value,$option);

        if(!$value instanceof File)
        static::throw('invalidFile');

        return parent::set($key,$value);
    }


    // add
    // permet d'ajouter plusieurs fichiers dans files
    // possible aussi de fournir un autre objet files
    final public function add(...$values):self
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
    final public function dirMethod(string $method,...$args):self
    {
        if(strpos($method,'get') !== 0)
        static::throw('invalidMethod',$method);

        $files = Base\Dir::$method(...$args);

        if(is_array($files))
        {
            foreach ($files as $v)
            {
                if(!is_dir($v))
                $this->set(null,$v);
            }
        }

        return $this;
    }


    // dir
    // ajoute le contenu de tout un directoire dans l'objet
    // utilise la méthode dir::get
    final public function dir($path,bool $dig=false,?array $option=null):self
    {
        return $this->dirMethod('get',$path,$dig,$option);
    }


    // dirVisible
    // ajoute tous les fichiers visibles du répertoire dans l'objet
    // utilise la méthode dir::getVisible
    final public function dirVisible($path,bool $dig=false,?array $option=null):self
    {
        return $this->dirMethod('getVisible',$path,$dig,$option);
    }


    // dirExtension
    // ajoute le contenu de tout un directoire dans l'objet dont l'extension est celle fourni
    // utilise la méthode dir::getExtension
    final public function dirExtension($path,$extension=null,bool $dig=false,?array $option=null):self
    {
        return $this->dirMethod('getExtension',$path,$extension,$dig,$option);
    }


    // concatenate
    // permet de concatener tous les fichiers et retourner le rendu dans un fichier
    final public function concatenate($target,?\Closure $closure=null,string $separator=PHP_EOL,?array $option=null):File
    {
        $return = File::newCreate($target,$option);
        Base\Res::concatenate($return->resource(),$closure,$separator,...$this->pair('resource'));
        return $return;
    }


    // concatenateString
    // permet de concatener tous les fichiers et retourner le rendu combiné dans une string
    final public function concatenateString(?\Closure $closure=null,string $separator=PHP_EOL):?string
    {
        return Base\Res::concatenateString($closure,$separator,...$this->pair('resource'));
    }


    // unlink
    // efface tous les fichiers de l'objet
    // retourne le nombre de fichier effacés
    final public function unlink():int
    {
        $return = 0;

        foreach ($this->arr() as $file)
        {
            if($file->unlink() === true)
            $return++;
        }

        return $return;
    }


    // makeUploadArray
    // retourne un tableau multidimensionnel de upload array à partir de l'objet fichiers
    // possible de remplir les trous
    final public function makeUploadArray(bool $fill=false):array
    {
        $return = [];

        foreach ($this->arr() as $key => $file)
        {
            $return[$key] = $file->makeUploadArray();
        }

        if($fill === true)
        $return = Base\Arr::keysMissing($return,Base\File::makeUploadArrayEmpty(),0);

        return $return;
    }


    // zip
    // permet d'archiver tous les fichiers dans un zip
    // peut envoyer des exceptions
    // retourne la resource zip
    final public function zip($value,?string $local=null,?array $option=null):File\Zip
    {
        $return = File\Zip::new($value,['create'=>true]) ?: static::throw('cannotCreateZipArchive');

        if(!$return->addFiles($this,$option))
        static::throw('couldNotAddFilesToZipArchive');

        $return->commit();

        return $return;
    }


    // uploadArrayReformat
    // méthode statique qui permet de reformatter un tableau de files
    // pouvant contenir des objets files ou file
    // possible de remplir les trous
    final public static function uploadArrayReformat(array $return,bool $fill=false):array
    {
        $uploadEmpty = Base\File::makeUploadArrayEmpty();

        foreach ($return as $key => $value)
        {
            if($value instanceof self)
            $return[$key] = $value->makeUploadArray();

            elseif(is_array($value))
            {
                foreach ($value as $k => $v)
                {
                    if($v instanceof File)
                    $return[$key][$k] = $v->makeUploadArray();
                }
            }

            if($fill === true && Base\Arr::isIndexed($return[$key]))
            $return[$key] = Base\Arr::keysMissing($return[$key],$uploadEmpty,0);
        }

        return $return;
    }
}
?>