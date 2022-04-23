<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main;
use Quid\Base;

// extender
// class for a collection containing a list of classes extending others
class Extender extends Map
{
    // trait
    use Map\_classe;
    use Map\_readOnly;


    // config
    protected static array $config = [
        'methodIgnore'=>null, // nom d'une méthode statique, si elle retourne true il faut ignorer la classe
        'onlyClass'=>true, // la méthode dans base/autoload charge seulement les classes à partir du nom de fichier
        'noSubDir'=>false, // envoie une exception dans onAddNamespace si un dossier contient un sous-directoire
        'subClass'=>null, // si les classes doivent étendre une subClass
        'exists'=>false, // fait une vérification si la classe existe
        'overloadKeyPrepend'=>null, // permet de spécifier une overload key, et ne pas avoir à charger la classe
        'mustExtend'=>false // les classes du même nom doivent étendre celles déjà dans l'objet
    ];


    // dynamique
    protected ?array $mapAllow = ['set','unset','remove','filter','serialize','clone']; // méthodes permises
    protected array $mapAfter = ['extendSync']; // appelé après chaque changement à l'objet
    protected array $extend = []; // garde en mémoire les clés de classe étendus
    protected array $overload = []; // garde en mémoire les overloads effectués


    // construct
    // construit l'objet
    final public function __construct($namespaces=null,?array $attr=null)
    {
        $this->makeAttr($attr);

        if(!empty($namespaces))
        {
            $namespaces = array_values((array) $namespaces);
            $this->addNamespace(...$namespaces);
        }
    }


    // onAddNamespace
    // callback après la méthode addNamespace
    final protected function onAddNamespace():void
    {
        return;
    }


    // isExtended
    // retourne vrai si la clé est étendu
    // doit se retrouver dans le tableau extend
    final public function isExtended($value):bool
    {
        return is_string($value) && array_key_exists($value,$this->extend);
    }


    // areSubClassOf
    // retourne vrai si toutes les classes sont des sous classes de
    final public function areSubClassOf(string $class):bool
    {
        return $this->every(fn($value) => is_subclass_of($value,$class,true));
    }


    // add
    // ajoute une ou plusieurs classes à l'objet
    // si des objets sont fournis, prend la classe
    final public function add(...$values):self
    {
        $values = $this->prepareValues(...$values);

        foreach ($values as $value)
        {
            if(is_object($value))
            $value = $value::class;

            $this->set(null,$value);
        }

        return $this;
    }


    // addNamespace
    // ajoute toutes les classes d'un namespace à l'objet
    // considère les classes existantes et celles dans base/autoload closure
    // ne creuse pas dans les sous-dossiers
    final public function addNamespace(string ...$values):self
    {
        $onlyClass = $this->getAttr('onlyClass');
        $noSubDir = $this->getAttr('noSubDir');

        foreach ($values as $value)
        {
            if(!empty($value))
            {
                $classes = Autoload::findMany($value,false,$onlyClass,true);

                if(!empty($classes))
                {
                    if($noSubDir === true)
                    static::checkNoSubDir($classes,false);

                    $this->add(...array_values($classes));
                }
            }
        }

        $this->onAddNamespace();

        return $this;
    }


    // firstNotSubClassOf
    // retourne la première classe qui n'est pas une sous-classe de
    final public function firstNotSubClassOf(string $class):?string
    {
        return $this->find(fn($value) => !is_subclass_of($value,$class,true));
    }


    // checkSubClassOf
    // envoie une exception si une des classes de l'étendeur n'est pas une sous-classe de la classe donnée en argument
    final public function checkSubClassOf(string $class):self
    {
        $not = $this->firstNotSubClassOf($class);
        if(!empty($not))
        static::throw($not,'firstNotSubClassOf',$class);

        return $this;
    }


    // checkExtend
    // vérifie que les classes de date existent et étendent bien la valuer extend, si spécifié
    final public function checkExtend():self
    {
        foreach ($this as $key => $value)
        {
            if(!class_exists($value,true))
            static::throw('classNotExists',$value);

            if(array_key_exists($key,$this->extend))
            {
                $subClass = $this->extend[$key];

                if(!is_subclass_of($value,$subClass,true))
                static::throw($value,'notSubClassOf',$subClass);
            }
        }

        return $this;
    }


    // checkParentSameName
    // permet de vérifie que toutes les classes ont un parent avec le même nom
    final public function checkParentSameName():self
    {
        foreach ($this as $key => $value)
        {
            $parent = get_parent_class($value);
            $name = Base\Fqcn::name($value);
            $parentName = (!empty($parent))? Base\Fqcn::name($parent):null;
            $isBoot = (strpos($name,'Boot') !== false || (is_string($parentName) && strpos($parentName,'Boot') !== false));

            if($isBoot === false && ($parentName === null || ($name !== $parentName)))
            static::throw($value,'withParent',$parent,'shouldNotBeThere');
        }

        return $this;
    }


    // set
    // ajoute une classe à l'objet extender
    // exception envoyé si une classe existe déjà et que la nouvelle ne l'étend pas
    // les traits et interfaces sont ignorés
    final public function set($key,$value):self
    {
        $methodIgnore = $this->getAttr('methodIgnore');
        $subClass = $this->getAttr('subClass');
        $exists = $this->getAttr('exists');
        $mustExtend = $this->getAttr('mustExtend');
        $extend = false;

        if($key !== null)
        static::throw('onlyNullKeyAllowed');

        if(!is_string($value))
        static::throw('notString',$value);

        if($exists === true && !class_exists($value,true))
        static::throw('classNotExists',$value);

        if(!empty($subClass) && !is_subclass_of($value,$subClass,true))
        static::throw($value,'notSubClassOf',$subClass);

        $isIgnored = null;
        if(!empty($methodIgnore) && $value::$methodIgnore() === true)
        $isIgnored = true;

        $key = static::getKey($value);

        if($this->exists($key))
        {
            $class = $this->get($key);
            $extend = $class;

            if($value === $class)
            static::throw('alreadyIn',$value);

            if($mustExtend === true && !is_subclass_of($value,$class,true))
            static::throw($value,'toReplaceMustExtend',$class);

            if($isIgnored === true)
            {
                $this->unset($key);
                if(!empty($extend) && array_key_exists($key,$this->extend))
                unset($this->extend[$key]);
            }
        }

        if($isIgnored !== true)
        {
            parent::set($key,$value);

            if(!empty($extend) && !array_key_exists($key,$this->extend))
            $this->extend[$key] = $extend;
        }

        return $this;
    }


    // extended
    // retourne un clone de l'objet avec seulement les classes étendus
    final public function extended():self
    {
        return $this->filter(fn($value,$key) => $this->isExtended($key));
    }


    // extendSync
    // synchronise le tableau extend après chaque changement à l'objet
    final public function extendSync():self
    {
        $data =& $this->arr();
        foreach ($this->extend as $key => $oldClass)
        {
            if(!array_key_exists($key,$data))
            unset($this->extend[$key]);
        }

        return $this;
    }


    // overload
    // passe chaque classe dans l'objet et ajoute la dans base/autoload overload
    // garde en mémoire le overload dans la propriété overload
    final public function overload():self
    {
        $prepend = $this->getAttr('overloadKeyPrepend');

        foreach ($this->arr() as $value)
        {
            if(is_object($value))
            $value = $value::class;

            if(is_string($prepend) && !empty($prepend))
            {
                $key = Base\Fqcn::name($value);
                $key = Base\Fqcn::append($prepend,$key);
            }

            elseif($value::classHasMethod('getOverloadKey'))
            $key = $value::getOverloadKey();

            else
            $key = Base\Fqcn::name($value);

            $this->overload[$key] = $value;
            Autoload::setOverload($key,$value);
        }

        return $this;
    }


    // overloadSync
    // synchronise le tableau overload avec la classe base/autoload
    final public function overloadSync():self
    {
        foreach ($this->overload as $key => $value)
        {
            Autoload::setOverload($key,$value);
        }

        return $this;
    }


    // alias
    // créer un alias pour chaque classe
    // le pattern vient de la classe main/autoload
    // si extend est true, prend le nom de classe originale avant qu'il soit étendu
    final public function alias(?string $pattern=null,bool $extend=true):self
    {
        if($pattern === null)
        $ending = Autoload::aliasEnding();

        foreach ($this->arr() as $key => $value)
        {
            $replace = null;

            if($extend === true && array_key_exists($key,$this->extend))
            $replace = $this->extend[$key];

            if(empty($replace))
            $replace = Base\Fqcn::name($value);

            $alias = $replace.$ending;
            Autoload::setAlias($alias,$value);
        }

        return $this;
    }


    // getKey
    // retourne la clé à utiliser pour la map
    final public static function getKey($value)
    {
        return Base\Fqcn::name($value);
    }


    // checkNoSubDir
    // envoie une exception si un des chemins contient un sous-directoire
    // test juste le premier directoire si all est false
    final protected static function checkNoSubDir(array $values,bool $all=false):void
    {
        foreach ($values as $key => $value)
        {
            if(is_string($key))
            {
                $dirname = dirname($key);
                if(Base\Dir::isDeep($dirname))
                static::throw('subdirectoryNotAllowed',$dirname);

                if($all === false)
                break;
            }
        }
    }
}
?>