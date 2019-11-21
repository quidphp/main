<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main\Contract;

// meta
// interface to describe methods making an objet a source of meta-data
interface Meta
{
    // metaTitle
    // retourne les données pour le meta title
    public function getMetaTitle($value=null);


    // metaKeywords
    // retourne les données pour le meta keywords
    public function getMetaKeywords($value=null);


    // metaDescription
    // retourne les données pour le meta description
    public function getMetaDescription($value=null);


    // metaImage
    // retourne les données pour le meta image
    public function getMetaImage($value=null);

    
    // getHtmlAttr
    // retourne les données des attributs de html
    public function getHtmlAttr($value=null);
    
    
    // getBodyAttr
    // retourne les données des attributs de body
    public function getBodyAttr($value=null);
}
?>