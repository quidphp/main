<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main\Contract;

// hierarchy
// interface to describe the methods required to access the hierarchy of an objet
interface Hierarchy
{
    // keyParent
    // retourne un tableau unidimensionnel avec le nom comme clé et le nom du parent comme valeur
    public function keyParent():array;


    // hierarchy
    // retourne le tableau de la hiérarchie des éléments de l'objet
    public function hierarchy(bool $exists=true);


    // childsRecursive
    // retourne un tableau avec tous les enfants de l'élément de façon récursive
    public function childsRecursive($value,bool $exists=true);


    // tops
    // retourne un objet des éléments n'ayant pas de parent
    public function tops();


    // parent
    // retourne l'objet d'un élément parent ou null
    public function parent($value);


    // top
    // retourne le plus haut parent d'un élément ou null
    public function top($value);


    // parents
    // retourne un objet avec tous les parents de l'élément
    public function parents($value);


    // breadcrumb
    // retourne un objet inversé de tous les parents de l'élément et l'objet courant
    public function breadcrumb($value);


    // siblings
    // retourne un objet des éléments ayant le même parent que la valeur donnée
    public function siblings($value);


    // childs
    // retourne un objet avec les enfants de l'élément donné en argument
    public function childs($value);
}
?>