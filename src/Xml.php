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

// xml
// class that provides basic methods to make an XML sitemap
class Xml extends Root
{
    // config
    protected static array $config = [];


    // dynamique
    protected \SimpleXMLElement $xml; // garde une copie de l'objet simpleXml


    // construct
    // construit l'objet xml, le urlset doit être fourni en argument
    final public function __construct(string $urlset)
    {
        $urlset = Base\Xml::urlset($urlset) ?? $urlset;
        $this->xml = new \SimpleXMLElement($urlset);
    }


    // toString
    // retourne le output du xml
    final public function __toString():string
    {
        return $this->output();
    }


    // clone
    // clone est permis
    final public function __clone()
    {
        return;
    }


    // cast
    // retourne le output du xml
    final public function _cast()
    {
        return $this->output();
    }


    // xml
    // retourne l'objet simpleXml
    final public function xml():\SimpleXMLElement
    {
        return $this->xml;
    }


    // output
    // output le xml en string
    final public function output():string
    {
        return $this->xml->asXML();
    }


    // sitemap
    // ajoute des uris dans le document xml
    // utilisé pour générer un sitemap
    final public function sitemap(string ...$values):self
    {
        $xml = $this->xml();

        foreach ($values as $value)
        {
            $child = $xml->addChild('url');
            $child->addChild('loc',$value);
        }

        return $this;
    }
}
?>