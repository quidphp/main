<?php
declare(strict_types=1);
namespace Quid\Main;
use Quid\Base;

// xml
class Xml extends Root
{
	// config
	public static $config = [];
	
	
	// dynamique
	protected $xml = null; // garde une copie de l'objet simpleXml
	
	
	// construct
	// construit l'objet xml, le urlset doit être fourni en argument
	public function __construct(string $urlset) 
	{
		$urlset = Base\Xml::urlset($urlset) ?? $urlset;
		$this->xml = new \SimpleXMLElement($urlset);
		
		return;
	}
	
	
	// toString
	// retourne le output du xml
	public function __toString():string
	{
		return $this->output();
	}
	
	
	// clone
	// clone est permis
	public function __clone()
	{
		return;
	}
	
	
	// cast
	// retourne le output du xml
	public function _cast()
	{
		return $this->output();
	}
	
	
	// xml
	// retourne l'objet simpleXml
	public function xml():\SimpleXMLElement
	{
		return $this->xml;
	}
	
	
	// output
	// output le xml en string
	public function output():string 
	{
		return $this->xml->asXML();
	}
	
	
	// sitemap
	// ajoute des uris dans le document xml
	// utilisé pour générer un sitemap 
	public function sitemap(string ...$values):self 
	{
		$xml = $this->xml();
		
		foreach ($values as $value) 
		{
			$child = $xml->addChild("url");
			$child->addChild("loc",$value);
		}
		
		return $this;
	}
}
?>