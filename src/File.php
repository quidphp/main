<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main;
use Quid\Base;

// file
// class for a basic file object
class File extends Res
{
    // config
    protected static array $config = [
        'group'=>null, // groupe par défaut, par défaut rien, si tu mets false la classe getClass ne cherchera pas de classe
        'mime'=>null, // définit le mime par défaut à utiliser (par exemple lors de la création d'une ressource temporaire)
        'create'=>false, // crée le fichier si non existant
        'read'=>null, // option pour read
        'write'=>null // option pour write
    ];


    // param
    protected static array $param = [
        'storageClass'=>[ // défini les classes storages, un dirname dans celui défini de la classe doit utilisé un objet particulier
            'cache'=>File\Cache::class,
            'error'=>File\Error::class,
            'log'=>File\Log::class,
            'queue'=>File\Queue::class,
            'session'=>File\Session::class],
        'utilClass'=>[ // défini les classes utilités
            'dump'=>File\Dump::class,
            'serialize'=>File\Serialize::class,
            'email'=>File\Email::class],
        'groupClass'=>[ // défini la classe à utiliser selon le mimeGroup du fichier
            'audio'=>File\Audio::class,
            'calendar'=>File\Calendar::class,
            'css'=>File\Css::class,
            'csv'=>File\Csv::class,
            'doc'=>File\Doc::class,
            'font'=>File\Font::class,
            'html'=>File\Html::class,
            'imageRaster'=>File\ImageRaster::class,
            'imageVector'=>File\ImageVector::class,
            'js'=>File\Js::class,
            'json'=>File\Json::class,
            'pdf'=>File\Pdf::class,
            'php'=>File\Php::class,
            'txt'=>File\Txt::class,
            'video'=>File\Video::class,
            'xml'=>File\Xml::class,
            'zip'=>File\Zip::class]
    ];


    // construct
    // construit l'objet fichier
    final public function __construct($value,?array $attr=null)
    {
        $attr = $this->prepareOption($value,$attr);
        $this->makeAttr($attr);
        parent::__construct($value,$attr);
    }


    // isResourceValid
    // méthode qui détermine si la resource est courante valide pour la classe
    final public function isResourceValid():bool
    {
        $return = false;
        $mimeGroup = static::defaultMimeGroup();
        $resource = $this->resource();

        if(is_string($mimeGroup))
        $return = (Base\Res::isEmpty($resource) || (Base\Res::isMimeGroup($group,$resource)));

        else
        $return = (Base\Res::isEmpty($resource) || Base\Res::isFileLike($resource));

        return $return;
    }


    // checkResourceValid
    // envoie une exception si la resource courante n'est pas valide pour la classe
    final public function checkResourceValid():self
    {
        if(!$this->isResourceValid())
        static::throw();

        return $this;
    }


    // prepareOption
    // essaie d'attribuer un mime à utiliser si non défini
    // retourne le tableau option
    final protected function prepareOption($value,?array $option=null):array
    {
        $return = (array) $option;

        if(empty($option['mime']))
        {
            if(!Base\File::is($value) && !Base\Res::is($value))
            {
                $mime = null;

                if(is_string($value))
                $mime = Base\Path::mime($value);

                if(empty($mime))
                $mime = static::defaultExtension();

                if(is_string($mime))
                $return['mime'] = $mime;
            }
        }

        return $return;
    }


    // readOption
    // retourne les options à utiliser lors du read
    final public function readOption():?array
    {
        return $this->getAttr('read');
    }


    // writeOption
    // retourne les options à utiliser pour écrire dans l'objet
    final public function writeOption():?array
    {
        return $this->getAttr('write');
    }


    // unlinkOnShutdown
    // permet de unlink le fichier au shutdown, s'il existe toujours
    final public function unlinkOnShutdown():void
    {
        Base\File::unlinkOnShutdown($this->path());
    }


    // makeUploadArray
    // retourne un upload array à partir d'un fichier
    // envoie une exception si le retour est null
    final public function makeUploadArray(int $error=0,bool $is=true):array
    {
        $return = Base\File::makeUploadArray($this,$error,$is);

        if(!is_array($return))
        static::throw();

        return $return;
    }


    // files
    // créer un nouvel objet files et met le fichier courant dedans
    final public function files(...$args):Files
    {
        $return = static::newFiles(...$args);
        $return->add($this);

        return $return;
    }


    // defaultMimeGroup
    // retourne le mime groupe par défaut pour la classe
    final public static function defaultMimeGroup():?string
    {
        return static::$config['group'] ?? null;
    }


    // defaultExtension
    // retourne l'extension par défaut selon le mime groupe de la classe
    final public static function defaultExtension():?string
    {
        $return = null;
        $group = static::defaultMimeGroup();

        if(!empty($group))
        $return = Base\Mime::extensionFromGroup($group);

        return $return;
    }


    // getClass
    // retourne la classe à utiliser avec la méthode new
    final public static function getClass($value,?array $option=null):string
    {
        $return = static::class;
        $option = Base\Arr::plus(['fromPath'=>false],$option);

        if(!in_array($return,static::$param['storageClass'],true) && !in_array($return,static::$param['groupClass'],true) && !in_array($return,static::$param['utilClass'],true))
        {
            $dirname = static::getDirnameFromValue($value);
            $storage = null;
            if(!empty($dirname))
            $storage = static::getClassFromDirname($dirname);

            if(!empty($storage))
            $return = $storage;

            else
            {
                if(is_string($value) && $option['fromPath'] === true)
                $group = Base\Mime::groupFromBasename($value);

                else
                {
                    $group = Base\Mime::getGroup($value);

                    if(empty($group) && !empty($option['mime']))
                    $group = Base\Mime::group($option['mime']);

                    if(empty($group) && !empty($option['basename']) && is_string($option['basename']))
                    $group = Base\Mime::groupFromBasename($option['basename']);
                }

                if(empty($group))
                $group = static::defaultMimeGroup();

                if(is_string($group))
                $return = static::getClassFromGroup($group) ?? static::class;
            }
        }

        if(!empty($return))
        $return = $return::classOverload();

        return $return;
    }


    // getClassFromGroup
    // retourne la classe à utiliser à partir du groupe
    final public static function getClassFromGroup(string $group):?string
    {
        return (array_key_exists($group,static::$param['groupClass']))? static::$param['groupClass'][$group]:null;
    }


    // getDirnameFromValue
    // retourne le dirname à partir d'une valeur passé dans file::new
    final public static function getDirnameFromValue($value):?string
    {
        $return = null;

        if(is_string($value))
        $return = Base\Path::dirname($value);

        elseif(Base\Res::is($value))
        $return = Base\Res::dirname($value);

        elseif($value instanceof self)
        $return = $value->dirname();

        return $return;
    }


    // getClassFromDirname
    // retourne la classe à utiliser à partir du dirname (en lien avec storageClass)
    final public static function getClassFromDirname(string $value):?string
    {
        $return = null;

        foreach (static::$param['storageClass'] as $class)
        {
            if(!is_a($class,Contract\FileStorage::class,true))
            static::throw($class,'mustImplement',Contract\FileStorage::class);

            $storage = $class::storageDirname();
            if($storage === $value || Base\Finder::isParent($storage,$value))
            {
                $return = $class;
                break;
            }
        }

        return $return;
    }


    // new
    // crée un nouvel objet fichier
    // la bonne classe sera utilisé pour la création, ceci est déterminé via le mimeGroup
    // il est possible de créer un fichier inexistant dans cette classe, si option a create=>true
    // exception envoyé si le fichier n'est pas créable
    // si value est déjà une instance de file, retourne l'objet directement
    final public static function new($value,?array $option=null):self
    {
        $return = null;

        if($value instanceof self)
        $return = $value;

        else
        {
            $class = static::getClass($value,$option);
            $return = new $class($value,$option);
        }

        return $return;
    }


    // newCreate
    // comme new, mais create est true par défaut
    final public static function newCreate($value,?array $option=null):self
    {
        return static::newOverload($value,Base\Arr::plus($option,['create'=>true]));
    }


    // newOverload
    // fait un overload sur la classe et ensuite passe à new
    final public static function newOverload(...$values):Root
    {
        return static::classOverload()::new(...$values);
    }


    // newFiles
    // retourne un nouvel objet files, overload
    final public static function newFiles(...$args):Files
    {
        return Files::newOverload(...$args);
    }


    // registerClass
    // permet d'enregister une classe fichier
    // méthode à étendre
    final public static function registerClass():void
    {
        return;
    }


    // registerGroup
    // permet d'enregister un nouveau mime et mettre la classe dans group
    final public static function registerGroup(string $name,string $mime,$extension,$families=null):void
    {
        Base\Mime::register($mime,$extension,$name,$families);
        static::$param['groupClass'][$name] = static::class;
    }


    // registerStorage
    // permet d'enregister une classe de storage
    final public static function registerStorage(string $name):void
    {
        static::$param['storageClass'][$name] = static::class;
    }


    // registerUtil
    // permet d'enregister une classe util
    final public static function registerUtil(string $name):void
    {
        static::$param['utilClass'][$name] = static::class;
    }


    // getOverloadKeyPrepend
    // retourne le prepend de la clé à utiliser pour le tableau overload
    final public static function getOverloadKeyPrepend():?string
    {
        return (static::class !== self::class && !Base\Fqcn::sameName(static::class,self::class))? 'File':null;
    }
}
?>