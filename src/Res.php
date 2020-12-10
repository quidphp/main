<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main;
use Quid\Base;

// res
// class with methods to manage a resource
class Res extends ArrObj
{
    // config
    protected static array $config = [];


    // base
    protected static array $base = [ // tableau des méthodes en clé et condtion (check) en valeur
        'isEmpty'=>null,
        'isNotEmpty'=>null,
        'isReadable'=>null,
        'isWritable'=>null,
        'isBinary'=>null,
        'isStream'=>null,
        'isRegularType'=>null,
        'isFinfo'=>null,
        'isContext'=>null,
        'isFile'=>null,
        'isFileExists'=>null,
        'isFileLike'=>null,
        'isFileUploaded'=>null,
        'isFileVisible'=>null,
        'isFilePathToUri'=>null,
        'isFileParentExists'=>null,
        'isFileParentReadable'=>null,
        'isFileParentWritable'=>null,
        'isFileParentExecutable'=>null,
        'isDir'=>null,
        'isHttp'=>null,
        'isPhp'=>null,
        'isPhpWritable'=>null,
        'isPhpInput'=>null,
        'isPhpOutput'=>null,
        'isPhpTemp'=>null,
        'isPhpMemory'=>null,
        'isResponsable'=>null,
        'isLocal'=>null,
        'isRemote'=>null,
        'isTimedOut'=>null,
        'isBlocked'=>null,
        'isSeekable'=>null,
        'isSeekableTellable'=>null,
        'isLockable'=>null,
        'isStart'=>null,
        'isEnd'=>null,
        'canStat'=>null,
        'canLocal'=>null,
        'canMeta'=>null,
        'canContext'=>null,
        'hasScheme'=>null,
        'hasExtension'=>null,
        'stat'=>null,
        'inode'=>null,
        'permission'=>null,
        'owner'=>null,
        'group'=>null,
        'dateAccess'=>null,
        'dateModify'=>null,
        'dateInodeModify'=>null,
        'info'=>null,
        'responseMeta'=>null,
        'type'=>null,
        'kind'=>null,
        'meta'=>null,
        'mode'=>null,
        'wrapperType'=>null,
        'wrapperData'=>null,
        'streamType'=>null,
        'unreadBytes'=>null,
        'uri'=>null,
        'headers'=>null,
        'parse'=>null,
        'scheme'=>null,
        'host'=>null,
        'path'=>null,
        'pathinfo'=>null,
        'dirname'=>null,
        'basename'=>null,
        'safeBasename'=>null,
        'mimeBasename'=>null,
        'filename'=>null,
        'extension'=>null,
        'size'=>null,
        'mime'=>null,
        'mimeGroup'=>null,
        'mimeFamilies'=>null,
        'mimeFamily'=>null,
        'param'=>null,
        'contextOption'=>null,
        'pathToUri'=>'isReadable',
        'pathToUriOrBase64'=>'isReadable',
        'position'=>'isSeekableTellable',
        'lineCount'=>'isSeekableTellable',
        'passthru'=>'isReadable',
        'base64'=>'isReadable',
        'lock'=>'isLockable',
        'unlock'=>'isLockable',
        'flush'=>'isWritable',
        'writeBom'=>'isWritable',
        'download'=>'isResponsable',
        'toScreen'=>'isResponsable',
        'concatenate'=>'isWritable',
        'parseEol'=>'isSeekableTellable',
        'findEol'=>'isSeekableTellable',
        'getEolLength'=>'isSeekableTellable',
        'getContextMime'=>null,
        'getContextBasename'=>null,
        'getContextEol'=>null
    ];


    // dynamique
    protected $resource = null; // conserve la resource


    // construct
    // construit l'objet resource
    // une resource ou chemin de resource à ouvrir doit être fourni en argument
    public function __construct($value,?array $option=null)
    {
        $this->setResource($value,$option);
    }


    // toString
    // retourne le contenu de la resource sous forme de string
    public function __toString():string
    {
        return $this->read();
    }


    // call
    // si une méthode est appelé et qu'elle n'est pas défini
    // renvoie vers base/res si la méthode est allouée
    final public function __call(string $method,array $args)
    {
        return $this->base($method,true,...$args);
    }


    // jsonSerialize
    // serialize l'objet avec json_encode
    // encode le tableau des lignes de la resource
    final public function jsonSerialize()
    {
        return $this->read();
    }


    // cast
    // retourne la resource
    public function _cast()
    {
        return $this->resource();
    }


    // toArray
    // retourne le tableau des lignes de la resource
    public function toArray():array
    {
        return $this->lines();
    }


    // offsetSet
    // ajoute ou change la valeur d'une clé dans la resource
    final public function offsetSet($key,$value):void
    {
        $arr = Base\Arr::set($key,$value,$this->arr());
        $this->overwrite($arr);
    }


    // offsetUnset
    // enlève une clé dans la resource
    // envoie une exception si non existant
    final public function offsetUnset($key):void
    {
        if(!$this->offsetExists($key))
        static::throw('arrayAccess','doesNotExist');

        $arr = Base\Arr::unset($key,$this->arr());
        $this->overwrite($arr);
    }


    // arr
    // retourne le tableau des lignes
    // envoie une exception si pas readable ni seekableTellable
    final protected function arr():array
    {
        $this->check('isReadable','isSeekableTellable');
        return $this->lines();
    }


    // isResourceValid
    // retourne vrai si la resource lié est valid
    public function isResourceValid():bool
    {
        return Base\Res::is($this->resource());
    }


    // setResource
    // sauve les paramètre de création de la resource dans l'objet
    // la resource est crée lors de l'appel à la méthode resource
    public function setResource($value,?array $option=null):void
    {
        $this->resource = [$value,$option];
    }


    // unsetResource
    // délie la resource de l'objet
    final public function unsetResource():void
    {
        $this->resource = null;
    }


    // setFromResource
    // méthode utilisé par toutes les méthodes rename pour resources
    final protected function setFromResource($value):self
    {
        if(!is_resource($value))
        static::throw();

        $this->setResource($value);

        return $this;
    }


    // returnFromBase
    // méthode protégé utilisé pour le retour de méthodes, envoie une exception si la valeur n'est pas true
    final protected function returnFromBase($value,...$args):self
    {
        if($value !== true)
        static::throw(...$args);

        return $this;
    }


    // resource
    // retourne la resource
    // crée la resource si pas encore existante
    final public function resource()
    {
        $return = null;
        $remember = null;

        if(is_resource($this->resource))
        $return = $this->resource;

        elseif(is_array($this->resource))
        {
            $value = $remember = $this->resource[0];
            $option = $this->resource[1];
            $this->resource = null;

            if($value instanceof self)
            $value = $value->resource();

            elseif(!is_resource($value))
            $value = Base\Res::open($value,$option);

            if(is_resource($value))
            $return = $this->resource = $value;
        }

        if(!is_resource($return))
        static::throw('cannotOpen',$remember);

        return $this->resource;
    }


    // base
    // permet de faire un appel à la classe base/res
    // les méthodes permises sont dans le tableau statique base
    final protected function base(string $method,bool $exception=true,...$args)
    {
        $return = null;
        $found = false;

        if(array_key_exists($method,static::$base))
        {
            $found = true;
            $condition = static::$base[$method];

            if($condition !== null)
            {
                $condition = (array) $condition;

                foreach ($condition as $value)
                {
                    $this->check($value);
                }
            }

            foreach ($args as $key => $value)
            {
                if($value instanceof static)
                $args[$key] = $value->resource();
            }

            $return = Base\Res::$method($this->resource(),...$args);
        }

        if($exception === true && $found === false)
        static::throw('methodDoesNotExist',$method);

        return $return;
    }


    // check
    // envoie une exception si une méthode alloué ne retourne pas true
    // les méthodes définis ont priorités sur celles de base
    // possible de mettre plusieurs méthodes
    final public function check(string ...$methods):self
    {
        foreach ($methods as $method)
        {
            if($this->hasMethod($method) && $this->$method() !== true)
            static::throw($method);

            elseif($this->base($method,false) !== true)
            static::throw($method);
        }

        return $this;
    }


    // isScheme
    // retourne vrai si la resource a le scheme spécifié dans son uri
    // attention: certains types de resources, comme file peuvent être fonctionnelles sans avoir de scheme
    final public function isScheme(string $target):bool
    {
        return Base\Res::isScheme($target,$this->resource());
    }


    // isExtension
    // retourne vrai si la resource a l'extension spécifié dans son uri
    final public function isExtension($target):bool
    {
        return Base\Res::isExtension($target,$this->resource());
    }


    // isMimeGroup
    // retourne vrai si le mime type est du group spécifé
    final public function isMimeGroup($group,bool $fromPath=true):bool
    {
        return Base\Res::isMimeGroup($group,$this->resource(),$fromPath);
    }


    // isMimeFamily
    // retourne vrai si le mime type est de la famille spécifé
    final public function isMimeFamily($family,bool $fromPath=true):bool
    {
        return Base\Res::isMimeFamily($family,$this->resource(),$fromPath);
    }


    // isFilePermission
    // vérifie s'il est possible d'accéder à la resource fichier en lecture, écriture ou éxécution
    // possibilité de spécifier un user ou un groupe, par défaut le user et groupe courant
    final public function isFilePermission(string $type,$user=null,$group=null):bool
    {
        return Base\Res::isFilePermission($type,$this->resource(),$user=null,$group=null);
    }


    // isOwner
    // retourne vrai si l'utilisateur est propriétraire de la resource
    // si user est null, utilise l'utilisateur courant
    final public function isOwner($user=null):bool
    {
        return Base\Res::isOwner($this->resource(),$user);
    }


    // isGroup
    // retourne vrai si le groupe est le même que le groupe du fichier
    // si group est null, utilise le groupe courant
    final public function isGroup($group=null):bool
    {
        return Base\Res::isGroup($this->resource(),$group);
    }


    // setPhpContextOption
    // permet de lier une clé -> valeur à l'intérieur du contexte de la ressource
    // n'a pas besoin d'être phpWritable
    final public function setPhpContextOption(string $key,$value):self
    {
        return $this->returnFromBase(Base\Res::setPhpContextOption($key,$value,$this->resource()));
    }


    // setContextMime
    // permet de lier un mime au sein du contexte de la ressource
    final public function setContextMime(string $mime):self
    {
        return $this->returnFromBase(Base\Res::setContextMime($mime,$this->resource()));
    }


    // setContextBasename
    // permet de lier un basename au sein du contexte de la ressource
    final public function setContextBasename(string $basename):self
    {
        return $this->returnFromBase(Base\Res::setContextBasename($basename,$this->resource()));
    }


    // setContextEol
    // permet changer la valeur eol au sein du contexte de la ressource
    final public function setContextEol($separator):self
    {
        return $this->returnFromBase(Base\Res::setContextEol($separator,$this->resource()));
    }


    // getPhpContextOption
    // retourne une option de contexte ou null
    // possible de creuser dans le tableau ou mettre null comme clé (retourne tout le tableau php)
    final public function getPhpContextOption($key=null)
    {
        return Base\Res::getPhpContextOption($key,$this->resource());
    }


    // permissionChange
    // change la permission de la resource fichier
    // envoie une exception en cas d'échec
    final public function permissionChange($mode):self
    {
        return $this->returnFromBase(Base\Res::permissionChange($mode,$this->resource()));
    }


    // ownerChange
    // change le owner de la resource fichier
    // envoie une exception en cas d'échec
    final public function ownerChange($user):self
    {
        return $this->returnFromBase(Base\Res::ownerChange($user,$this->resource()));
    }


    // groupChange
    // change le groupe de la resource fichier
    // envoie une exception en cas d'échec
    final public function groupChange($group):self
    {
        return $this->returnFromBase(Base\Res::groupChange($group,$this->resource()));
    }


    // readOption
    // retourne les options pour lire
    public function readOption():?array
    {
        return null;
    }


    // read
    // lit le contenu de la resource en format brut
    final public function read($seek=0,$length=true,?array $option=null)
    {
        $this->check('isReadable');
        return Base\Res::read($seek,$length,$this->resource(),Base\Arr::plus($this->readOption(),$option));
    }


    // readRaw
    // lit le contenu de la resource en format brut
    // ne tient pas compte de readOption
    final public function readRaw($seek=0,$length=true,?array $option=null):string
    {
        $this->check('isReadable');
        return Base\Res::read($seek,$length,$this->resource(),$option);
    }


    // seek
    // permet de seek la resource
    final public function seek($seek=0,?int $type=SEEK_SET):self
    {
        $this->check('isSeekable');
        Base\Res::seek($seek,$this->resource(),$type);

        return $this;
    }


    // seekCurrent
    // déplace le pointeur de la resource à partir de sa position courante
    // position ne peut pas être PHP_INT_MAX pour SET_CUR
    final public function seekCurrent($seek):self
    {
        $this->check('isSeekable');
        Base\Res::seekCurrent($seek,$this->resource());

        return $this;
    }


    // seekEnd
    // envoie le pointeur de la resource à la fin
    // ne fonctionne pas avec une ressource directoire
    final public function seekEnd($seek=0):self
    {
        $this->check('isSeekable');
        Base\Res::seekEnd($seek,$this->resource());

        return $this;
    }


    // seekRewind
    // rewind le pointeur de la resource au début
    // contrairement à seek, rewind ne vérifie pas la fin de la resource avec une lecture une bit plus loin
    final public function seekRewind():self
    {
        $this->check('isSeekable');
        Base\Res::seekRewind($this->resource());

        return $this;
    }


    // lines
    // retourne un tableau des lignes de la resource
    final public function lines($offset=0,$length=true,?array $option=null):?array
    {
        $return = Base\Res::lines($offset,$length,$this->resource(),Base\Arr::plus($this->readOption(),$option));
        return $this->lineReturns($return);
    }


    // line
    // retourne la ligne courante de la resource
    final public function line(?array $option=null)
    {
        $return = Base\Res::line($this->resource(),Base\Arr::plus($this->readOption(),$option));
        return $this->lineReturn($return);
    }


    // lineRef
    // retourne la ligne courante de la resource à partir d'un offset, length et un i
    // le i doit être passé par référence
    final public function lineRef($offset,$length,int &$i,?array $option=null)
    {
        $return = Base\Res::lineRef($this->resource(),$offset,$length,$i,Base\Arr::plus($this->readOption(),$option));
        return $this->lineReturn($return);
    }


    // lineFirst
    // retourne la première ligne de la resource
    final public function lineFirst(?array $option=null)
    {
        $return = Base\Res::lineFirst($this->resource(),Base\Arr::plus($this->readOption(),$option));
        return $this->lineReturn($return);
    }


    // lineLast
    // retourne la dernière ligne de la resource
    final public function lineLast(?array $option=null)
    {
        $return = Base\Res::lineLast($this->resource(),Base\Arr::plus($this->readOption(),$option));
        return $this->lineReturn($return);
    }


    // lineChunk
    // permet de subdiviser le tableau de l'ensemble des lignes de la resource par longueur
    // retourne un tableau multidimensionnel colonne
    final public function lineChunk(int $each,bool $preserve=true,?array $option=null):?array
    {
        $return = Base\Res::lineChunk($each,$this->resource(),$preserve,Base\Arr::plus($this->readOption(),$option));

        foreach ($return as $key => $value)
        {
            $return[$key] = $this->lineReturns($value);
        }

        return $return;
    }


    // lineChunkWalk
    // permet de subdiviser le tableau de l'ensemble des lignes de la resource selon le retour d'un callback
    // si callback retourne true, la colonne existante est stocké et une nouvelle colonne est crée
    // si callback retourne faux, la colonne existante est stocké et fermé
    // si callback retourne null, la ligne est stocké si la colonne est ouverte, sinon elle est ignoré
    // retourne un tableau multidimensionnel colonne
    final public function lineChunkWalk(\Closure $callback,?array $option=null):?array
    {
        $return = Base\Res::lineChunkWalk($callback,$this->resource(),Base\Arr::plus($this->readOption(),$option));

        foreach ($return as $key => $value)
        {
            $return[$key] = $this->lineReturns($value);
        }

        return $return;
    }


    // lineReturns
    // gère la valeur de retour pour lines
    final protected function lineReturns(array $return):array
    {
        foreach ($return as $key => $value)
        {
            $return[$key] = $this->lineReturn($value);
        }

        return $return;
    }


    // lineReturn
    // gère la valeur de retour pour line
    protected function lineReturn($return)
    {
        return $return;
    }


    // passthruChunk
    // lit le contenu d'une resource en la divisant par une longueur
    // la resource est immédiatement envoyé dans le buffer via echo
    // possibilité de sleep entre chaque longueur
    // retourne le nombre de chunk de données envoyés ou null si la resource n'est pas lisible
    // ne peut pas être une resource directoire
    // option clean, rewind, flush et sleep
    final public function passthruChunk($length,?array $option=null):?int
    {
        return Base\Res::passthruChunk($length,$this->resource(),$option);
    }


    // subCount
    // retourne le nombre d'occurences d'une substring dans une ressource
    // si sub contient le separateur, la recherche se fait dans tout le fichier et non pas par ligne
    // les fichiers csv seront traités en tant que string et non pas array
    final public function subCount(string $sub,?array $option=null):?int
    {
        return Base\Res::subCount($sub,$this->resource(),$option);
    }


    // writeOption
    // retourne les options à utiliser pour écrire dans l'objet
    public function writeOption():?array
    {
        return null;
    }


    // write
    // écrit du contenu dans une ressource à l'endoit où est le pointeur
    // possibilité de barrer la ressource pendant l'opération
    // possibilité de flush le buffer pour que le contenu soit écrit immédiatement dans la ressource
    // envoie une exception si le contenu n'a pas été écrit en entier
    final public function write($content,?array $option=null):self
    {
        return $this->returnFromBase(Base\Res::write($content,$this->resource(),Base\Arr::plus($this->writeOption(),$option)));
    }


    // writeRaw
    // écrit le contenu de la resource en format brut
    // ne tient pas compte de writeOption
    final public function writeRaw($content,?array $option=null):self
    {
        return $this->returnFromBase(Base\Res::write($content,$this->resource(),$option));
    }


    // overwrite
    // effacer le contenu de la ressource et ensuite écrit le nouveau contenu
    // envoie une exception si le contenu n'a pas été écrit en entier
    final public function overwrite($content,?array $option=null):self
    {
        return $this->returnFromBase(Base\Res::overwrite($content,$this->resource(),Base\Arr::plus($this->writeOption(),$option)));
    }


    // prepend
    // prepend du contenu dans une ressource
    // si newline est true, ajoute une newline à la fin du nouveau contenu
    // envoie une exception si le contenu n'a pas été écrit en entier
    final public function prepend($content,?array $option=null):self
    {
        return $this->returnFromBase(Base\Res::prepend($content,$this->resource(),Base\Arr::plus($this->writeOption(),$option)));
    }


    // append
    // append du contenu dans une ressource
    // envoie une exception si le contenu n'a pas été écrit en entier
    final public function append($content,?array $option=null):self
    {
        return $this->returnFromBase(Base\Res::append($content,$this->resource(),Base\Arr::plus($this->writeOption(),$option)));
    }


    // lineSplice
    // permet d'enlever et éventuellement remplacer des lignes dans la ressource
    // offset accepte un chiffre négatif
    final public function lineSplice(int $offset,int $length,$replace=null,?array $option=null):self
    {
        Base\Res::lineSplice($offset,$length,$this->resource(),$replace,true,Base\Arr::plus($this->writeOption(),$option));
        return $this;
    }


    // lineSpliceFirst
    // permet d'enlever et éventuellement remplacer la première ligne de la ressource
    final public function lineSpliceFirst($replace=null,?array $option=null):self
    {
        Base\Res::lineSpliceFirst($this->resource(),$replace,true,Base\Arr::plus($this->writeOption(),$option));
        return $this;
    }


    // lineSpliceLast
    // permet d'enlever et éventuellement remplacer la dernière ligne de la ressource
    final public function lineSpliceLast($replace=null,?array $option=null):self
    {
        Base\Res::lineSpliceLast($this->resource(),$replace,true,Base\Arr::plus($this->writeOption(),$option));
        return $this;
    }


    // lineInsert
    // permet d'insérer du nouveau contenu à un numéro de ligne dans la ressource
    // le reste du contenu est repoussé
    // offset accepte un chiffre négatif
    final public function lineInsert(int $offset,$replace,?array $option=null):self
    {
        Base\Res::lineInsert($offset,$replace,$this->resource(),true,Base\Arr::plus($this->writeOption(),$option));
        return $this;
    }


    // lineFilter
    // permet de passer chaque ligne de la resource dans un callback
    // si le callback retourne faux, la ligne est retiré
    // la ressource est automatiquement modifié
    final public function lineFilter(\Closure $callback,?array $option=null):self
    {
        Base\Res::lineFilter($callback,$this->resource(),true,Base\Arr::plus($this->writeOption(),$option));
        return $this;
    }


    // lineMap
    // permet de passer chaque ligne de la resource dans un callback
    // la ligne est remplacé par la valeur de retour du callback
    final public function lineMap(\Closure $callback,?array $option=null):self
    {
        Base\Res::lineMap($callback,$this->resource(),true,Base\Arr::plus($this->writeOption(),$option));
        return $this;
    }


    // empty
    // vide une resource
    // size permet de définir quel taille la ressource doit avoir après l'opération, donc la méthode truncate à partir de la fin
    // possibilité de barrer la ressource pendant l'opération
    final public function empty(int $size=0,?array $option=null):self
    {
        Base\Res::empty($this->resource(),$size,$option);
        return $this;
    }


    // touch
    // touche une resource fichier et change les dates d'accès et de modification
    // envoie une exception en cas d'échec
    final public function touch():self
    {
        return $this->returnFromBase(Base\Res::touch($this->resource()));
    }


    // rename
    // renomme une resource fichier, retourne la nouvelle resource en cas de succès
    // met à jour la resource de l'objet ou envoie une exception
    final public function rename($target):self
    {
        return $this->setFromResource(Base\Res::rename($target,$this->resource()));
    }


    // changeDirname
    // renomme le dirname de la resource fichier, garde le basename
    // met à jour la resource de l'objet ou envoie une exception
    final public function changeDirname($dirname):self
    {
        return $this->setFromResource(Base\Res::changeDirname($dirname,$this->resource()));
    }


    // changeBasename
    // renomme le basename de la resource fichier, garde le dirname
    // met à jour la resource de l'objet ou envoie une exception
    final public function changeBasename(string $basename):self
    {
        return $this->setFromResource(Base\Res::changeBasename($basename,$this->resource()));
    }


    // changeExtension
    // change l'extension d'une resource fichier, garde le dirname et filename
    // met à jour la resource de l'objet ou envoie une exception
    final public function changeExtension(string $extension):self
    {
        return $this->setFromResource(Base\Res::changeExtension($extension,$this->resource()));
    }


    // removeExtension
    // enlève l'extension d'une resource fichier, garde le dirname et filename
    // met à jour la resource de l'objet ou envoie une exception
    final public function removeExtension():self
    {
        return $this->setFromResource(Base\Res::removeExtension($this->resource()));
    }


    // moveUploaded
    // déplace une resource fichier venant d'être chargé
    // met à jour la resource de l'objet ou envoie une exception
    final public function moveUploaded($target):self
    {
        return $this->setFromResource(Base\Res::moveUploaded($target,$this->resource()));
    }


    // copy
    // copy une resource fichier
    // la resource reste la même, envoie une exception en cas d'échec
    final public function copy($to):self
    {
        return $this->returnFromBase(Base\Res::copy($to,$this->resource()),'fileWasNotCopied',$to);
    }


    // copyInDirname
    // copy une resource fichier, garde le même dirname
    // la resource reste la même, envoie une exception en cas d'échec
    final public function copyInDirname(string $basename):self
    {
        return $this->returnFromBase(Base\Res::copyInDirname($basename,$this->resource()));
    }


    // copyWithBasename
    // copy une resource fichier, garde le même basename
    // la resource reste la même, envoie une exception en cas d'échec
    final public function copyWithBasename($dirname):self
    {
        return $this->returnFromBase(Base\Res::copyWithBasename($dirname,$this->resource()));
    }


    // unlink
    // efface le fichier de la resource, retourne un booléean
    // enlève la resource en cas de succès, sinon envoie une exception
    final public function unlink(bool $exception=true):bool
    {
        $return = Base\Res::unlink($this->resource());

        if($return === true)
        $this->unsetResource();

        elseif($exception === true)
        static::throw();

        return $return;
    }


    // toFile
    // écrire le contenu de la resource dans un nouveau fichier
    // envoie une exception si la resource n'est pas responsable
    // retourne un objet file, avec la bonne classe selon le mime group
    final public function toFile($value,?array $option=null):File
    {
        $option = Base\Arr::plus(['create'=>true],$option);
        $this->check('isResponsable');
        $mimeGroup = $this->mimeGroup();
        $class = File::class;

        if(is_string($mimeGroup))
        $class = File::getClassFromGroup($mimeGroup) ?? $class;

        $return = $class::newOverload($value,$option);
        return $return->write($this);
    }
}
?>