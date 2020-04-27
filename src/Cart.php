<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 * Readme: https://github.com/quidphp/main/blob/master/README.md
 */

namespace Quid\Main;
use Quid\Base;

// cart
// class used to to manage a cart containing different items
class Cart extends Map
{
    // trait
    use Map\_filter;


    // config
    protected static array $config = [];


    // dynamique
    protected ?array $mapAllow = ['unset','empty','filter','jsonSerialize','serialize','clone']; // méthodes permises


    // construct
    // construit l'objet
    final public function __construct(?array $values=null,?array $attr=null)
    {
        $this->makeAttr($attr);

        if(!empty($values))
        {
            foreach ($values as $value)
            {
                if(is_array($value))
                $this->add(...array_values($value));
            }
        }

        return;
    }


    // onPrepareValue
    // cast la valeur
    protected function onPrepareValue($return)
    {
        return Base\Obj::cast($return);
    }


    // hasItem
    // retourne vrai si le cart contient l'item au moins une fois
    final public function hasItem($value):bool
    {
        return $this->getIndexFromItem($value) !== null;
    }


    // getIndexFromItem
    // retourne le premier index du product dans le cart
    final public function getIndexFromItem($value,bool $strict=false,bool $allowEmpty=false):?int
    {
        $return = null;
        $indexes = $this->getIndexesFromItem($value);
        $count = count($indexes);

        if($strict === true)
        {
            if($count > 1 || ($allowEmpty === false && $count === 0))
            static::throw('invalidCountForItem',$value,$count);
        }

        if(!empty($count))
        $return = current($indexes);

        return $return;
    }


    // getIndexesFromItem
    // retourne tous les index du product dans le cart
    final public function getIndexesFromItem($value):array
    {
        $return = [];
        $value = $this->onPrepareValue($value);

        foreach ($this->arr() as $index => $array)
        {
            if($array['item'] === $value)
            $return[] = $index;
        }

        return $return;
    }


    // getItemFromIndex
    // permet de retourner un item via un index
    final public function getItemFromIndex(int $index)
    {
        return $this->get($index)['item'] ?? null;
    }


    // getQuantityFromIndex
    // permet de retourner une quantité via un index
    final public function getQuantityFromIndex(int $index):?int
    {
        return $this->get($index)['quantity'] ?? null;
    }


    // getQuantityFromItem
    // permet de retourner une quantité via un item
    // envoie une exception si plus d'un même item dans le cart
    final public function getQuantityFromItem($value):?int
    {
        $return = null;
        $index = $this->getIndexFromItem($value,true,true);

        if($index !== null)
        $return = $this->getQuantityFromIndex($index);

        return $return;
    }


    // add
    // permet d'ajouter un item dans le cart
    final public function add($value,int $quantity=1,?array $attr=null):self
    {
        return $this->commit(null,$value,$quantity,$attr);
    }


    // update
    // permet de mettre à jour la quantité ou les attributs à partir d'un item
    // attention les attributs sont remplacés
    final public function update($value,int $quantity,?array $attr=null)
    {
        return $this->updateIndex($this->getIndexFromItem($value,true,false),$quantity,$attr);
    }


    // updateQuantity
    // permet de mettre à jour la quantité à partir d'un item
    // les attributs ne sont pas remplacés
    final public function updateQuantity($value,int $quantity)
    {
        return $this->updateIndexQuantity($this->getIndexFromItem($value,true,false),$quantity);
    }


    // updateRelative
    // permet de mettre à jour la quantité ou les attributs de façon relative à partir d'un item
    // c'est à dire que la quantité est additionner ou supprimer, les attributs sont merges
    final public function updateRelative($value,?int $quantity=null,?array $attr=null):self
    {
        return $this->updateIndexRelative($this->getIndexFromItem($value,true,false),$quantity,$attr);
    }


    // addOrUpdate
    // ajoute l'item si non existant dans le cart, sinon envoie à update
    final public function addOrUpdate($value,int $quantity=1,?array $attr=null):self
    {
        return ($this->hasItem($value))? $this->update($value,$quantity,$attr):$this->add($value,$quantity,$attr);
    }


    // updateIndex
    // permet de mettre à jour la quantité ou les attributs à partir d'un index
    // attention les attributs sont remplacés
    final public function updateIndex(int $index,int $quantity,?array $attr=null):self
    {
        $this->checkExists($index);
        $data = $this->arr();
        $value = $data[$index]['item'];

        return $this->commit($index,$value,$quantity,$attr);
    }


    // updateIndexQuantity
    // permet de mettre à jour la quantité à partir d'un index
    // les attributs ne sont pas remplacés
    final public function updateIndexQuantity(int $index,int $quantity):self
    {
        $this->checkExists($index);
        $data = $this->arr();
        ['item'=>$value,'attr'=>$attr] = $data[$index];

        return $this->commit($index,$value,$quantity,$attr);
    }


    // updateIndexRelative
    // permet de mettre à jour la quantité ou les attributs de façon relative à partir d'un index
    // c'est à dire que la quantité est additionner ou supprimer, les attributs sont merges
    final public function updateIndexRelative(int $index,?int $quantity=null,?array $attr=null):self
    {
        $this->checkExists($index);
        $data = $this->arr();
        $array = $data[$index];
        $value = $array['item'];

        if(is_int($quantity))
        $quantity = $array['quantity'] + $quantity;

        if(is_array($attr))
        $attr = Base\Arrs::replace($array['attr'],$attr);

        return $this->commit($index,$value,$quantity,$attr);
    }


    // commit
    // méthode protégé qui fait des changements au tableau de l'objet
    final protected function commit(?int $index=null,$value,int $quantity,?array $attr=null):self
    {
        $value = $this->onPrepareValue($value);

        if(!empty($value))
        {
            if($quantity > 0)
            {
                $array = ['item'=>$value,'quantity'=>$quantity,'attr'=>$attr];
                $data =& $this->arr();

                if(is_int($index))
                $data[$index] = $array;
                else
                $data[] = $array;
            }

            elseif(is_int($index))
            $this->unset($index);
        }

        return $this;
    }
}
?>