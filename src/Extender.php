<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main;
use Quid\Base;

// extender
// class for a collection containing a listing of classes extending other ones
class Extender extends Map
{
    // trait
    use _option;
    use Map\_filter;
    use Map\_readOnly;


    // config
    public static $config = [
        'option'=>[
            'type'=>'class', // l'extender emmagasine des noms de classe ou des objets
            'methodIgnore'=>null, // nom d'une méthode statique, si elle retourne true il faut ignorer la classe
            'onlyClass'=>true, // la méthode dans base/autoload charge seulement les classes à partir du nom de fichier
            'subClass'=>null, // si les classes doivent étendre une subClass
            'exists'=>true, // fait une vérification si la classe existe
            'overloadKeyPrepend'=>null, // permet de spécifier une overload key, et ne pas avoir à charger la classe
            'mustExtend'=>true, // les classes du même nom doivent étendre celles déjà dans l'objet
            'args'=>null] // permet de spécifier les arguments lors de la création de l'objet, si type est obj
    ];


    // map
    protected static $allow = ['set','unset','remove','filter','serialize','clone']; // méthodes permises
    protected static $after = ['extendSync']; // appelé après chaque changement à l'objet


    // dynamique
    protected $extend = []; // garde en mémoire les clés de classe étendus
    protected $overload = []; // garde en mémoire les overloads effectués


    // construct
    // construit l'objet
    public function __construct($namespaces=null,?array $option=null)
    {
        $this->option($option);

        if(!empty($namespaces))
        {
            $namespaces = array_values((array) $namespaces);
            $this->addNamespace(...$namespaces);
        }

        return;
    }


    // onPrepareThis
    // retourne l'objet cloner pour la méthode filter
    public function onPrepareThis(string $method):Map
    {
        return ($method === 'filter')? $this->clone():$this;
    }


    // onAddNamespace
    // callback après la méthode addNamespace
    // méthode protégé
    protected function onAddNamespace():self
    {
        return $this;
    }


    // isExtended
    // retourne vrai si la clé est étendu
    // doit se retrouver dans le tableau extend
    public function isExtended($value):bool
    {
        return (is_string($value) && array_key_exists($value,$this->extend))? true:false;
    }


    // add
    // ajoute une ou plusieurs classes à l'objet
    // si des objets sont fournis, prend la classe
    public function add(...$values):self
    {
        $values = $this->prepareValues(...$values);

        foreach ($values as $value)
        {
            if(is_object($value))
            $value = get_class($value);

            $this->set(null,$value);
        }

        return $this;
    }


    // addNamespace
    // ajoute toutes les classes d'un namespace à l'objet
    // considère les classes existantes et celles dans base/autoload closure
    // ne creuse pas dans les sous-dossiers
    public function addNamespace(string ...$values):self
    {
        $onlyClass = $this->getOption('onlyClass');

        foreach ($values as $value)
        {
            if(!empty($value))
            {
                $classes = Autoload::findMany($value,false,$onlyClass,true);

                if(!empty($classes))
                $this->add(...array_values($classes));
            }
        }

        $this->onAddNamespace();

        return $this;
    }


    // set
    // ajoute une classe à l'objet extender
    // exception envoyé si une classe existe déjà et que la nouvelle ne l'étend pas
    // les traits et interfaces sont ignorés
    public function set($key,$value):parent
    {
        $type = $this->getOption('type');
        $methodIgnore = $this->getOption('methodIgnore');
        $subClass = $this->getOption('subClass');
        $exists = $this->getOption('exists');
        $mustExtend = $this->getOption('mustExtend');
        $args = $this->getOption('args');
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

            if($type === 'obj')
            $class = get_class($class);

            if($value === $class)
            static::throw('alreadyIn',$value);

            if($mustExtend === true)
            {
                if(!is_subclass_of($value,$class,true))
                static::throw($value,'toReplaceMustExtend',$class);

                else
                $extend = $class;
            }

            if($isIgnored === true)
            {
                $this->unset($key);
                if(!empty($extend) && array_key_exists($key,$this->extend))
                unset($this->extend[$key]);
            }
        }

        if($isIgnored !== true)
        {
            if($type === 'obj')
            {
                $args = array_values((array) $args);
                $value = new $value(...$args);
            }

            parent::set($key,$value);

            if(!empty($extend) && !array_key_exists($key,$this->extend))
            $this->extend[$key] = $extend;
        }

        return $this;
    }


    // extended
    // retourne un clone de l'objet avec seulement les classes étendus
    public function extended():self
    {
        return $this->filter(function($value,$key) {
            return ($this->isExtended($key))? true:false;
        });
    }


    // extendSync
    // synchronise le tableau extend après chaque changement à l'objet
    public function extendSync():self
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
    public function overload():self
    {
        $prepend = $this->getOption('overloadKeyPrepend');

        foreach ($this->arr() as $value)
        {
            if(is_object($value))
            $value = get_class($value);

            if(is_string($prepend) && !empty($prepend))
            {
                $key = Base\Fqcn::name($value);
                $key = Base\Fqcn::append($prepend,$key);
            }

            elseif(method_exists($value,'getOverloadKey'))
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
    public function overloadSync():self
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
    public function alias(?string $pattern=null,bool $extend=false):self
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
    public static function getKey($value)
    {
        return Base\Fqcn::name($value);
    }
}
?>