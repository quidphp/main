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

// session
// class for a session storage file, which is serialized
class Session extends Serialize implements Main\Contract\Session, Main\Contract\FileStorage
{
    // config
    protected static array $config = [];


    // storageDirname
    // retounre le dirname du storage, le dossier ou les fichiers sessions sont gargés
    final public static function storageDirname():string
    {
        return Base\Session::getSavePath(true);
    }


    // sessionSid
    // retourne la clé de session
    final public function sessionSid():string
    {
        return $this->filename();
    }


    // sessionData
    // retourne les données de la session
    final public function sessionData():string
    {
        return $this->readRaw();
    }


    // sessionWrite
    // écrit de nouvelles données dans le fichier session
    final public function sessionWrite(string $data):bool
    {
        $this->overwrite($data,['callback'=>null]);
        return true;
    }


    // sessionUpdateTimestamp
    // update le timestamp du fichier session, retourne true même si rien n'a changé
    final public function sessionUpdateTimestamp():bool
    {
        $this->touch();

        return true;
    }


    // sessionDestroy
    // détruit le fichier de session
    final public function sessionDestroy():bool
    {
        return $this->unlink();
    }


    // sessionDir
    // retourne le directoire de session
    final public static function sessionDir(string $path,string $name):string
    {
        $return = null;

        if(!empty($path) && !empty($name))
        $return = Base\Path::append($path,$name);

        return $return;
    }


    // sessionPath
    // retourne le chemin de session
    final public static function sessionPath(string $path,string $name,string $sid):string
    {
        $return = null;

        if(!empty($path) && !empty($name) && !empty($sid))
        $return = Base\Path::append($path,$name,$sid);

        return $return;
    }


    // sessionExists
    // retourne vrai si le sid exists pour le nom donné
    final public static function sessionExists(string $path,string $name,string $sid):bool
    {
        $path = static::sessionPath($path,$name,$sid);
        return Base\File::is($path);
    }


    // sessionCreate
    // crée une nouvelle session avec le nom et side donné
    // retourne une classe qui implémente Contract\Session
    final public static function sessionCreate(string $path,string $name,string $sid):?Main\Contract\Session
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
    final public static function sessionRead(string $path,string $name,string $sid):?Main\Contract\Session
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
    final public static function sessionGarbageCollect(string $path,string $name,int $lifetime,$not=null):int
    {
        $return = 0;
        $dir = static::sessionDir($path,$name);
        $timestamp = Base\Datetime::now() - $lifetime;
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

// init
Session::__init();
?>