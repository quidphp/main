<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main;
use Quid\Base;

// _inst
// trait that provides methods to store an instantiated objet within a static property
trait _inst
{
    // inst
    protected static $inst = []; // tableau avec les instances


    // onPrepareSetInst
    // prépare l'objet qui sera ajouté au inst
    final protected function onPrepareSetInst():void
    {
        return;
    }


    // onSetInst
    // callback après l'ajout d'un objet dans inst
    final protected function onSetInst():void
    {
        return;
    }


    // onPrepareUnsetInst
    // prépare l'objet qui sera sorti du inst
    final protected function onPrepareUnsetInst():void
    {
        return;
    }


    // onUnsetInst
    // callback après le retrait d'un objet de inst
    final protected function onUnsetInst():void
    {
        return;
    }


    // inInst
    // retourne vrai si l'objet courant est dans inst
    final public function inInst():bool
    {
        return (in_array($this,static::$inst,true))? true:false;
    }


    // checkInst
    // envoie une exception si l'objet courant n'est pas dans inst
    final public function checkInst():self
    {
        if(!$this->inInst())
        static::throw();

        return $this;
    }


    // setInst
    // ajoute l'objet au tableau inst
    // onPrepareSetInst appelé avant l'ajout dans inst
    // onSetInst appelé après l'ajout dans inst
    // une exception est lancé si un objet du même nom existe déjà
    // si instname retourne une valeur identique peu importe l'objet, inst ne peut contenir qu'un objet
    final public function setInst():self
    {
        $value = $this->instName();

        if(!array_key_exists($value,static::$inst))
        {
            $this->onPrepareSetInst();
            static::$inst[$value] = $this;
            $this->onSetInst();
        }

        elseif(array_key_exists($value,static::$inst) && static::$inst[$value] !== $this)
        static::throw('nameAlreadyExistsForAnother');

        else
        static::throw('nameAlreadyExists');

        return $this;
    }


    // unsetInst
    // enlève l'objet du tableau inst
    // onPrepareUnsetInst appelé avant le retrait de inst
    // onUnsetInst appelé après le retrait de inst
    // une exception est lancé si l'objet n'est pas dans inst
    final public function unsetInst():self
    {
        $value = $this->instName();

        if(is_string($value) && array_key_exists($value,static::$inst))
        {
            $this->onPrepareUnsetInst();
            unset(static::$inst[$value]);
            $this->onUnsetInst();
        }

        else
        static::throw('nameDoesNotExists');

        return $this;
    }


    // hasInst
    // retourne vrai s'il y a au moins une instance
    final public static function hasInst():bool
    {
        return (!empty(static::$inst))? true:false;
    }


    // isInst
    // retourne vrai si l'instance existe
    // peut soumettre une instance, le nom ou l'index
    final public static function isInst($value=0):bool
    {
        $return = false;

        if(is_int($value) && array_key_exists($value,array_keys(static::$inst)))
        $return = true;

        elseif(is_string($value) && array_key_exists($value,static::$inst))
        $return = true;

        elseif($value instanceof self && in_array($value,static::$inst,true))
        $return = true;

        return $return;
    }


    // inst
    // retourne une instance de la classe par instance, nom ou index
    // une exception est envoyé si le retour n'est pas une instance de la classe
    final public static function inst($value=0,bool $throw=true):?self
    {
        $return = null;

        if(is_int($value))
        $value = Base\Arr::indexKey($value,static::$inst);

        if(is_string($value) && array_key_exists($value,static::$inst))
        $return = static::$inst[$value];

        elseif($value instanceof self && in_array($value,static::$inst,true))
        $return = $value;

        if($throw === true && !$return instanceof self)
        static::throw('empty');

        return $return;
    }


    // instSafe
    // retourne une instance de la classe par instance, nom ou index
    // une exception n'est jamais envoyé
    final public static function instSafe($value=0)
    {
        return static::inst($value,false);
    }


    // isReady
    // retourne vrai si la classe est ready
    // méthode qui doit être étendu
    final public function isReady():bool
    {
        return false;
    }


    // instReady
    // retourne l'instance de la classe si prête
    // la méthode isReady est utilisé pour déterminer si la classe est prête
    // n'envoie pas d'exception
    final public static function instReady($value=0):?self
    {
        $return = null;

        if(static::isInst($value))
        {
            $inst = static::inst($value);
            if(!empty($inst) && $inst->isReady())
            $return = $inst;
        }

        return $return;
    }


    // instName
    // génère le nom de inst pour la classe
    // si le nom est le même pour tous les objets, il ne sera possible que d'ajouter un seul objet dans inst
    final public function instName():string
    {
        return static::class;
    }


    // insts
    // retourne le tableau inst
    final public static function insts():array
    {
        return static::$inst;
    }


    // instNew
    // crée une nouvelle instance de la classe et place dans inst
    final public static function instNew(...$values):self
    {
        return (new static(...$values))->setInst();
    }
}
?>