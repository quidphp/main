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

// _nav
// trait that provides methods to a collection in order to work with pagination
trait _nav
{
    // pageSlice
    // permet de slice l'objet à partir d'une page et d'une limite
    final public function pageSlice(int $page,int $limit):self
    {
        $slice = Base\Nav::pageSlice($page,$limit,$this->arr());
        return (is_array($slice))? $this->gets(...array_keys($slice)):new static();
    }


    // pageFirst
    // retourne la première page
    final public function pageFirst(int $limit):?int
    {
        return Base\Nav::pageFirst($this->arr(),$limit);
    }


    // pagePrev
    // retourne la page précédente
    final public function pagePrev(int $page,int $limit):?int
    {
        return Base\Nav::pagePrev($page,$this->arr(),$limit);
    }


    // pageNext
    // retourne la page suivante
    final public function pageNext(int $page,int $limit):?int
    {
        return Base\Nav::pageNext($page,$this->arr(),$limit);
    }


    // pageLast
    // retourne la dernière page
    final public function pageLast(int $limit):?int
    {
        return Base\Nav::pageLast($this->arr(),$limit);
    }


    // general
    // retourne un tableau contenant un maximum d'informations relatives aux pages
    final public function general(int $page,int $limit,int $amount=3):?array
    {
        return Base\Nav::general($page,$this->arr(),$limit,$amount);
    }
}
?>