<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main\Map;
use Quid\Base;
use Quid\Main;

// _classeObj
// trait that gives methods to work with a collection containing fqcn strings or objects
trait _classeObj
{
    // trait
    use _filter;


    // dynamique
    protected ?string $mapSortDefault = null; // défini la méthode pour sort par défaut


    // classeOrObj
    // retourne si le trait doit utilisé l'appelation de classe :: ou d'objet ->
    abstract public static function classeOrObj():string;


    // onPrepareThis
    // retourne l'objet cloner pour certaines méthodes
    final protected function onPrepareThis(string $method):Main\Map
    {
        return (in_array($method,['filter','sortBy'],true))? $this->clone():$this;
    }


    // pair
    // retourne un tableau associatif avec le clé et le résultat d'une méthode
    final public function pair(string $method,...$args):array
    {
        $return = [];
        $type = static::classeOrObj();

        foreach ($this->arr() as $key => $value)
        {
            if($type === 'obj')
            $return[$key] = $value->$method(...$args);

            else
            $return[$key] = $value::$method(...$args);
        }

        return $return;
    }


    // group
    // permet de grouper le contenu en multiples classes ou objets groupé par la valeur d'un résultat de méthode
    // la valeur doit être key, donc null et booléean envoie une exception
    // retourne un tableau avec des classes ou objets
    final public function group(string $method,...$args):array
    {
        $return = [];
        $type = static::classeOrObj();

        foreach ($this->arr() as $key => $value)
        {
            if($type === 'obj')
            $k = $value->$method(...$args);
            else
            $k = $value::$method(...$args);

            if(is_object($k))
            $k = Base\Obj::cast($k);

            if(!Base\Arr::isKey($k))
            static::throw('notAnArrayKey',$value);

            if(!array_key_exists($k,$return))
            $return[$k] = new static();

            $data =& $return[$k]->arr();
            $data[$key] = $value;
        }

        return $return;
    }


    // sortBy
    // sort l'objet par le résultat d'une méthode
    // clone l'objet
    final public function sortBy($method,bool $sort=true,...$args):self
    {
        $this->checkAllowed('sort');
        $return = $this->onPrepareThis('sortBy');
        $type = static::classeOrObj();
        $data =& $return->arr();

        if($type === 'obj')
        $data = Base\Obj::sort($method,$sort,$data,...$args);
        else
        $data = Base\Classe::sort($method,$sort,$data,...$args);

        return $return->checkAfter();
    }


    // sortDefault
    // ramène l'objet dans son sort naturel
    // la méthode doit être défini dans la propriété static sortDefault
    // ne clone pas l'objet
    final public function sortDefault():self
    {
        $this->checkAllowed('sort');
        $return = $this->onPrepareThis('sort');
        $type = static::classeOrObj();
        $data =& $return->arr();
        $sort = $this->mapSortDefault;

        if($type === 'obj')
        $data = Base\Obj::sort($sort,true,$data);
        else
        $data = Base\Classe::sort($sort,true,$data);

        return $return->checkAfter();
    }
}
?>