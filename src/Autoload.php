<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main;
use Quid\Base;

// autoload
// class that provides custom autoloading logic, including alias autoloading and class within closures
class Autoload extends Base\Root
{
    // config
    protected static array $config = [
        'alias'=>[], // liste d'alias pour le lazy load
        'aliasEnding'=>'Alias', // fin de nom de classe pour les alias automatique
        'closure'=>[], // tableau de nom de classe avec closure, pour charger des classes sans inclure de fichiers
        'overload'=>[] // garde une copie des noms de classe overloadés
    ];


    // dynamique
    protected array $attr = []; // attribut de construction
    protected array $hit = []; // classe trouvé
    protected ?array $miss = null; // classe non trouvé


    // construct
    // construit l'objet autoload
    final public function __construct(string $type,bool $miss=false,?string $init=null,?array $arg=null)
    {
        $attr = ['type'=>$type,'miss'=>$miss,'init'=>$init,'arg'=>$arg];
        $this->setAttr($attr);
    }


    // setAttr
    // conserve les attributs de l'objet autoload
    final protected function setAttr(array $value):void
    {
        $this->attr = $value;
    }


    // attr
    // retourne le tableau d'attribut de l'objet autoload
    final public function attr():array
    {
        return $this->attr;
    }


    // type
    // retourne le type de l'objet autoload
    final public function type():string
    {
        return $this->attr()['type'];
    }


    // initClass
    // initialise une classe lors du chargement
    // la méthode est spécifié dans les attr
    final protected function initClass(string $class):bool
    {
        $return = false;
        $init = $this->attr()['init'];

        if(is_string($init) && method_exists($class,$init))
        {
            $class::$init();
            $return = true;
        }

        return $return;
    }


    // storeHit
    // conserve une classe qui été trouvé
    // une clé peut être donné, sinon c'est null
    final protected function storeHit(?string $key,string $value):void
    {
        if(is_string($key))
        $this->hit[$key] = $value;

        else
        $this->hit[] = $value;
    }


    // storeMiss
    // conserve une classe qui n'a pas été trouvé si attribut miss est true
    final protected function storeMiss(string $value):void
    {
        if($this->attr()['miss'] === true)
        {
            if(!is_array($this->miss))
            $this->miss = (array) $this->miss;

            if(!in_array($value,$this->miss,true))
            $this->miss[] = $value;
        }
    }


    // getCallable
    // retourne la callable à utiliser pour l'objet autoload
    final public function getCallable():callable
    {
        $return = null;
        $type = $this->type();

        if($type === 'psr4')
        $return = [$this,'findPsr4'];

        elseif($type === 'alias')
        $return = [$this,'findAlias'];

        elseif($type === 'closure')
        $return = [$this,'findClosure'];

        return $return;
    }


    // register
    // enregistre la callable dans le pool autoload
    final public function register(bool $prepend=false,bool $throw=true):bool
    {
        $return = false;
        $callable = $this->getCallable();

        if(!empty($callable))
        $return = spl_autoload_register($callable,$throw,$prepend);

        return $return;
    }


    // unregister
    // enlève la callable du pool autoload
    final public function unregister():bool
    {
        $return = false;
        $callable = $this->getCallable();

        if(!empty($callable))
        $return = spl_autoload_unregister($callable);

        return $return;
    }


    // findPsr4
    // callable utiliser lorsque le type est psr4
    // détermine si la classe existe
    final public function findPsr4(string $class):bool
    {
        $return = false;
        $file = $this->getPsr4File($class);

        if(!empty($file))
        {
            static::requireFile($file);

            if(static::exists($class))
            {
                $this->initClass($class);
                $return = true;
            }
        }

        if($return === false)
        $this->storeMiss($class);

        elseif($return === true)
        $this->storeHit(null,$class);

        return $return;
    }


    // getPsr4File
    // retourne le fichier à charger à partir d'une classe
    final public function getPsr4File(string $class):?string
    {
        $return = null;
        $psr4 = (array) $this->attr()['arg'];

        foreach ($psr4 as $key => $value)
        {
            if($class !== $key && strpos($class,$key) === 0)
            {
                $len = (strlen($key) + 1);
                $after = substr($class,$len);

                if(is_string($after) && strlen($after))
                {
                    $extension = static::phpExtension();
                    $after = str_replace('\\','/',$after);
                    $value .= '/'.$after.'.'.$extension;

                    if(file_exists($value))
                    {
                        $return = $value;
                        break;
                    }
                }
            }
        }

        return $return;
    }


    // findAlias
    // callable utiliser lorsque le type est alias
    // détermine si la classe non trouvé est un alias
    final public function findAlias(string $alias):bool
    {
        $return = false;
        $class = static::getAlias($alias,true);

        if(empty($class))
        {
            $ending = static::aliasEnding();
            $length = strlen($ending);

            if(substr($alias,-$length) === $ending)
            $class = substr($alias,0,-$length);
        }

        if(!empty($class))
        $return = class_alias($class,$alias,true);

        if($return === false)
        $this->storeMiss($alias);

        elseif($return === true)
        $this->storeHit($alias,$class);

        return $return;
    }


    // findClosure
    // callable utiliser lorsque le type est closure
    // autoload utilisé pour charger une classe gardé dans une closure danas static config
    // envoie à resolved si réussi
    final public function findClosure(string $class):bool
    {
        $return = false;
        $closure = static::getClosure($class,true);

        if(!empty($closure))
        {
            $closure();

            if(static::exists($class))
            {
                $this->initClass($class);
                $return = true;
            }
        }

        if($return === false)
        $this->storeMiss($class);

        elseif($return === true)
        $this->storeHit(null,$class);

        return $return;
    }


    // registerPsr4
    // créer et register un autoload avec chemins psr4
    // une fois register, envoie les autoload dans base/autoload
    final public static function registerPsr4(array $psr4,bool $miss=true,?string $init=null,bool $prepend=false):self
    {
        $return = new static('psr4',$miss,$init,$psr4);
        $return->register($prepend);

        Base\Autoload::setsPsr4($psr4);

        return $return;
    }


    // getAlias
    // retourne le nom de classe d'un alias, tel que spécifié dans static config
    // possible d'effacer la classe du tableau si trouvé
    final public static function getAlias(string $key,bool $delete=false):?string
    {
        $return = null;

        if(array_key_exists($key,static::$config['alias']))
        {
            $return = static::$config['alias'][$key];

            if($delete === true)
            unset(static::$config['alias'][$key]);
        }

        return $return;
    }


    // setAlias
    // change ou ajoute un alias
    // permet de faire le lazyload d'alias
    final public static function setAlias(string $key,string $value):void
    {
        static::$config['alias'][$key] = $value;
    }


    // setsAlias
    // change ou ajoute plusieurs alias
    // permet de faire le lazyload d'alias
    final public static function setsAlias(array $keyValue):void
    {
        foreach ($keyValue as $key => $value)
        {
            if(is_string($key) && is_string($value))
            static::$config['alias'][$key] = $value;
        }
    }


    // unsetAlias
    // enlève un alias de static config
    final public static function unsetAlias(string $key):void
    {
        if(array_key_exists($key,static::$config['alias']))
        unset(static::$config['alias'][$key]);
    }


    // allAlias
    // retourne le tableau des alias dans static config
    final public static function allAlias():array
    {
        return static::$config['alias'];
    }


    // emptyAlias
    // vide le tableau des alias
    final public static function emptyAlias():void
    {
        static::$config['alias'] = [];
    }


    // aliasEnding
    // retourne la fin de classe à utiliser pour les alias automatiques
    final public static function aliasEnding():string
    {
        return static::$config['aliasEnding'];
    }


    // registerAlias
    // créer et register un autoload pour alias
    final public static function registerAlias(bool $miss=false,bool $prepend=true):self
    {
        $return = new static('alias',$miss);
        $return->register($prepend);

        return $return;
    }


    // getClosure
    // retourne la closure stocké dans static config à partir d'un nom de classe
    // possible d'effacer la classe du tableau si trouvé
    final public static function getClosure(string $value,bool $delete=false):?\Closure
    {
        $return = null;
        $explode = explode('\\',$value);

        if(!empty($explode))
        {
            $copy = $explode;
            $className = array_pop($copy);
            $namespace = implode('\\',$copy);

            if(!empty($namespace) && !empty(static::$config['closure'][$namespace][$className]))
            {
                $return = static::$config['closure'][$namespace][$className];

                if($delete === true)
                unset(static::$config['closure'][$namespace][$className]);
            }
        }

        return $return;
    }


    // setClosure
    // permet de store une closure
    final public static function setClosure(string $namespace,string $class,\Closure $closure):void
    {
        static::$config['closure'][$namespace][$class] = $closure;
    }


    // getClosureByNamespace
    // permet de retourner un tableau avec toutes les closures restantes correspondants au namespace
    // possible de retirer les interfaces et les traits
    // possible de creuser dans le namespace si dig est true
    final public static function getClosureByNamespace(string $namespace,bool $onlyClass=false,bool $dig=false):array
    {
        $return = [];
        $looper = [];

        if($dig === false)
        {
            if(!empty(static::$config['closure'][$namespace]))
            $looper[$namespace] = static::$config['closure'][$namespace];
        }

        else
        {
            foreach (static::$config['closure'] as $ns => $classes)
            {
                if(stripos($ns,$namespace) === 0)
                $looper[$ns] = $classes;
            }
        }

        foreach ($looper as $ns => $classes)
        {
            foreach ($classes as $name => $closure)
            {
                if($onlyClass === false || Base\Classe::isNameClass($name))
                {
                    $fqcn = Base\Fqcn::str([$ns,$name]);
                    $return[] = $fqcn;
                }
            }
        }

        return $return;
    }


    // allClosure
    // retourne le tableau des closures
    final public static function allClosure():array
    {
        return static::$config['closure'];
    }


    // registerClosure
    // créer et register un autoload pour closure
    final public static function registerClosure(bool $miss=false,?string $init=null,bool $prepend=true):self
    {
        $return = new static('closure',$miss,$init);
        $return->register($prepend);

        return $return;
    }


    // getOverload
    // retourne le fqcn de la classe à utiliser avec la clé
    // permet d'obtenir la version surchargé de la classe
    // si vide, peut retourner la classe par défaut spécifié
    final public static function getOverload(string $key,?string $default=null):?string
    {
        return static::$config['overload'][$key] ?? $default;
    }


    // setOverload
    // ajout une classe à utiliser avec une clé
    final public static function setOverload(string $key,string $value):void
    {
        static::$config['overload'][$key] = $value;
    }


    // setsOverload
    // change ou ajoute plusieurs clés de classe overloadés
    final public static function setsOverload(array $keyValue):void
    {
        foreach ($keyValue as $key => $value)
        {
            if(is_string($key) && is_string($value))
            static::$config['overload'][$key] = $value;
        }
    }


    // unsetOverload
    // enlève un overload
    final public static function unsetOverload(string $key):void
    {
        if(array_key_exists($key,static::$config['overload']))
        unset(static::$config['overload'][$key]);
    }


    // allOverload
    // retourne le tableau de classes liés à des clés
    final public static function allOverload():array
    {
        return static::$config['overload'];
    }


    // emptyOverload
    // vide le tableau des overload
    final public static function emptyOverload():void
    {
        static::$config['overload'] = [];
    }


    // isRegistered
    // retourne vrai s'il y a un autoload du type fourni en argument présentement enregistré
    final public static function isRegistered(string $type):bool
    {
        $closure = fn($value) => is_array($value) && $value[0] instanceof self && $value[0]->type() === $type;
        return Base\Arr::some(Base\Autoload::all(),$closure);
    }


    // findNamespace
    // permet de retourner les namespaces à partir d'une closure
    // gère psr4, closure et declared
    // possible de sort le tableau de sortie
    final public static function findNamespace(?\Closure $callback=null,bool $closure=false,bool $declared=false,bool $sort=false):array
    {
        $return = array_keys(Base\Autoload::allPsr4($callback));

        if($closure === true)
        {
            foreach (static::$config['closure'] as $namespace => $classes)
            {
                if(!in_array($namespace,$return,true))
                {
                    if($callback === null || $callback($namespace) === true)
                    $return[] = $namespace;
                }
            }
        }

        if($declared === true)
        {
            $declared = Base\Classe::declared();
            $namespaces = Base\Classe::namespaces($declared);

            foreach ($namespaces as $namespace)
            {
                if(!in_array($namespace,$return,true))
                {
                    if($callback === null || $callback($namespace) === true)
                    $return[] = $namespace;
                }
            }
        }

        if($sort === true)
        asort($return);

        return $return;
    }


    // findOneOrMany
    // permet de fournir une ou plusieurs fqcns pouvant utilisés le caractère séparateur +
    // retourne un tableau unidimmensionnel avec tous les fichiers existants (en clé) et le fqcn (en valeur)
    // le dossier a priorité
    final public static function findOneOrMany($value,bool $dig=true,bool $onlyClass=false,bool $declared=false):array
    {
        $return = [];
        $value = (array) $value;

        foreach ($value as $target)
        {
            $target = Base\Fqcn::many($target);

            if(!empty($target))
            {
                foreach ($target as $v)
                {
                    if(is_string($v))
                    {
                        $array = [];

                        $dir = static::findMany($v,$dig,$onlyClass,$declared);
                        if(!empty($dir))
                        $array = $dir;

                        $file = static::findOne($v,$onlyClass,$declared);

                        if(!empty($file))
                        {
                            if($file === true)
                            $array[] = $v;

                            else
                            $array[$file] = $v;
                        }

                        $return = Base\Arr::imergeUnique($return,$array);
                    }
                }
            }
        }

        return $return;
    }


    // findOne
    // retourne le chemin du fichier à partir d'un fqcn
    // si declared est true et que la classe existe, retourne true
    final public static function findOne(string $value,bool $onlyClass=false,bool $declared=false)
    {
        $return = Base\Autoload::getFilePath($value,true);

        if(empty($return) && $declared === true && Base\Classe::isAny($value,true))
        $return = true;

        if(!empty($return) && $onlyClass === true && !class_exists($value,true))
        $return = null;

        return $return;
    }


    // findMany
    // retourne le chemin du premier dossier existant avec les fichiers à l'intérieur et leur fqcn associé
    // possible de désactiver les traits et les interfaces (par défaut inclut)
    // possible d'inclure les classes déclarés (chargés ou dans closure)
    final public static function findMany(string $value,bool $dig=false,bool $onlyClass=false,bool $declared=false):?array
    {
        $return = null;
        $path = Base\Autoload::getDirPath($value);

        if(!empty($path) && is_dir($path))
        {
            if($onlyClass === true)
            $opt = ['fqcnClass'=>true,'fqcnTrait'=>false,'fqcnInterface'=>false];
            else
            $opt = ['fqcnClass'=>true,'fqcnTrait'=>true,'fqcnInterface'=>true];

            $return = Base\Dir::getFqcn($path,$value,$dig,null,$opt);
        }

        if($declared === true)
        {
            $classes = Base\Classe::declared($value,$onlyClass,$dig);
            $classes = Base\Autoload::removeAlias($classes);
            $closures = static::getClosureByNamespace($value,$onlyClass,$dig);
            $return = Base\Arr::imergeUnique($classes,$closures,$return);
        }

        return $return;
    }


    // callNamespace
    // permet d'appeler une méthode sur chaque classe d'un namespace
    final public static function callNamespace(array $target,string $method,$exclude=null,?array $data=null,bool $sort=true):array
    {
        $return = [];
        $classes = static::findOneOrMany($target,true,true,true);

        if(!empty($exclude))
        $classes = Base\Arr::valuesStrip($exclude,$classes);

        if($sort === true)
        asort($classes);

        foreach ($classes as $key => $class)
        {
            if(!method_exists($class,$method))
            unset($classes[$key]);
        }

        $return = Base\Call::staticClasses($classes,$method,$data);

        return $return;
    }


    // requireFile
    // fait un require sur la file
    final protected static function requireFile(string $value):bool
    {
        require $value;
        return true;
    }


    // exists
    // retourne vrai si la classe existe, autoload est false
    final public static function exists(string $class):bool
    {
        return class_exists($class,false) || trait_exists($class,false) || interface_exists($class,false);
    }


    // phpExtension
    // retourne l'extension de php
    final public static function phpExtension():string
    {
        return 'php';
    }
}
?>