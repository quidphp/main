<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 * Readme: https://github.com/quidphp/main/blob/master/README.md
 */

namespace Quid\Main\Map;
use Quid\Base;
use Quid\Main;

// _arr
// trait that provides methods to allow advanced unidimensional array manipulation to a collection
trait _arr
{
    // are
    // retourne vrai si les clés fournis sont l'ensemble des clés de la map
    final public function are(...$keys):bool
    {
        return Base\Arr::keysAre($this->prepareKeys(...$keys),$this->arr(),static::isSensitive());
    }


    // append
    // ajoute une ou plusieurs valeurs à la fin du tableau
    // si la valeur est un tableau, les clés sont ajoutés au premier niveau de la map
    final public function append(...$values):Main\Map
    {
        $this->checkAllowed('append');
        $return = $this->onPrepareThis('append');
        $values = $return->prepareReplaces(...$values);
        $return->checkBefore(true,...$values);

        $data =& $return->arr();
        $data = Base\Arr::append($data,...$values);

        return $return->checkAfter();
    }


    // prepend
    // ajoute une ou plusieurs valeurs au début du tableau
    // si la valeur est un tableau, les clés sont ajoutés au premier niveau de la map
    final public function prepend(...$values):Main\Map
    {
        $this->checkAllowed('prepend');
        $return = $this->onPrepareThis('prepend');
        $values = $return->prepareReplaces(...$values);
        $return->checkBefore(true,...$values);

        $data =& $return->arr();
        $data = Base\Arr::prepend($data,...$values);

        return $return->checkAfter();
    }


    // pop
    // enlève une ou plusieurs valeurs à la fin du tableau
    // retourne les valeurs enlevés
    final public function pop(int $amount=1)
    {
        $this->checkAllowed('pop');
        $r = $this->onPrepareThis('pop');
        $data =& $r->arr();
        $return = Base\Arr::pop($data,$amount);
        $r->checkAfter();

        return $return;
    }


    // shift
    // enlève une ou plusieurs valeurs au début du tableau
    // retourne les valeurs enlevés
    final public function shift(int $amount=1)
    {
        $this->checkAllowed('shift');
        $r = $this->onPrepareThis('shift');
        $data =& $r->arr();
        $return = Base\Arr::shift($data,$amount);
        $r->checkAfter();

        return $return;
    }


    // replace
    // fait un merge replace avec d'autres valeurs
    // les valeurs doivent toutes être des tableaux après prepareReplaces
    final public function replace(...$values):Main\Map
    {
        $this->checkAllowed('replace');
        $return = $this->onPrepareThis('replace');
        $values = $return->prepareReplaces(...$values);
        $return->checkBefore(true,...$values);

        if(Base\Arr::validate('array',$values))
        {
            $data =& $return->arr();
            $data = Base\Arr::replace($data,...$values);
        }

        else
        static::throw('requireArray');

        return $return->checkAfter();
    }


    // splice
    // efface et remplace des slices de la map en utilisant start et end
    final public function splice($start,$end,$replace=null):Main\Map
    {
        $this->checkAllowed('splice');
        $return = $this->onPrepareThis('splice');
        $replace = $return->onPrepareReplace($replace);

        if(is_array($replace) || $replace === null)
        {
            if(empty($replace) || $return->checkBefore(true,$replace))
            {
                $data =& $return->arr();
                $data = Base\Arr::splice($return->onPrepareKey($start),$return->onPrepareKey($end),$data,$replace,static::isSensitive());
            }
        }

        else
        static::throw('replaceMustBeArrayOrNull');

        return $return->checkAfter();
    }


    // spliceIndex
    // efface et remplace des slices de la map en utilisant offset et length
    final public function spliceIndex(int $offset,?int $length,$replace=null):Main\Map
    {
        $this->checkAllowed('splice','index');
        $return = $this->onPrepareThis('splice');
        $replace = $return->onPrepareReplace($replace);

        if(is_array($replace) || $replace === null)
        {
            if(empty($replace) || $return->checkBefore(true,$replace))
            {
                $data =& $return->arr();
                $data = Base\Arr::spliceIndex($offset,$length,$data,$replace,static::isSensitive());
            }
        }

        else
        static::throw('replaceMustBeArrayOrNull');

        return $return->checkAfter();
    }


    // insert
    // effectue un remplacement via la méthode splice, n'enlève aucune rangée du tableau
    final public function insert($start,$replace):Main\Map
    {
        $this->checkAllowed('insert');
        $return = $this->onPrepareThis('insert');
        $replace = $return->onPrepareReplace($replace);

        if(is_array($replace) || $replace === null)
        {
            $return->checkBefore(true,$replace);

            $data =& $return->arr();
            $data = Base\Arr::insert($return->onPrepareKey($start),$replace,$data,static::isSensitive());
        }

        else
        static::throw('replaceMustBeArrayOrNull');

        return $return->checkAfter();
    }


    // insertIndex
    // effectue un remplacement via la méthode spliceIndex, n'enlève aucune rangée de la map
    final public function insertIndex(int $offset,$replace):Main\Map
    {
        $this->checkAllowed('insert','index');
        $return = $this->onPrepareThis('insert');
        $replace = $return->onPrepareReplace($replace);

        if(is_array($replace) || $replace === null)
        {
            $return->checkBefore(true,$replace);

            $data =& $return->arr();
            $data = Base\Arr::insertIndex($offset,$replace,$data,static::isSensitive());
        }

        else
        static::throw('replaceMustBeArrayOrNull');

        return $return->checkAfter();
    }
}
?>