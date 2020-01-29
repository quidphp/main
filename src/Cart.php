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
// class used to to manage a cart containing different elements
class Cart extends Map
{
    // trait
    use Map\_filter;


    // config
    public static $config = [];


    // map
    protected static $allow = ['unset','empty','filter','jsonSerialize','serialize','clone']; // méthodes permises


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


    // getItem
    // permet de retourner un item via un index
    final public function getItem(int $index)
    {
        return $this->get($index)['item'] ?? null;
    }


    // add
    // permet d'ajouter un item dans le cart
    final public function add($value,int $quantity=1,?array $attr=null):self
    {
        return $this->commit(null,$value,$quantity,$attr);
    }


    // update
    // permet de mettre à jour la quantité ou les attributs à partir d'un index
    // attention les attributs sont remplacés
    final public function update(int $index,int $quantity,?array $attr=null):self
    {
        $this->checkExists($index);
        $data = $this->arr();
        $value = $data[$index]['item'];

        return $this->commit($index,$value,$quantity,$attr);
    }


    // updateQuantity
    // permet de mettre à jour la quantité à partir d'un index
    // les attributs ne sont pas remplacés
    final public function updateQuantity(int $index,int $quantity):self
    {
        $this->checkExists($index);
        $data = $this->arr();
        ['item'=>$value,'attr'=>$attr] = $data[$index];

        return $this->commit($index,$value,$quantity,$attr);
    }


    // updateRelative
    // permet de mettre à jour la quantité ou les attributs de façon relative à partir d'un index
    // c'est à dire que la quantité est additionner ou supprimer, les attributs sont merges
    final public function updateRelative(int $index,?int $quantity=null,?array $attr=null):self
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


    // indexesFromItem
    // retourne tous les indexes qui contienennt l'item donné en argument
    final public function indexesFromItem($value):array
    {
        $return = [];
        $value = $this->onPrepareValue($value);

        foreach ($this as $key => $array)
        {
            if($array['item'] === $value)
            $return[] = $key;
        }

        return $return;
    }
}
?>