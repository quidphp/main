<?php
declare(strict_types=1);
namespace Quid\Main;

// widget
abstract class Widget extends Root
{
	// trait
	use _option;
	
	
	// config
	public static $config = array();
	
	
	// dynamique
	protected $callback = array(); // tableau de callback pour la classe


	// toString
	// renvoie le html de l'élément
	public function __toString():string 
	{
		return $this->html();
	}
	
	
	// clone
	// clone est permis
	public function __clone()
	{
		return;
	}
	
	
	// toArray
	// retourne la structure de l'élément sous forme de tableau multidimensionnel
	public function toArray():array 
	{
		return $this->structure();
	}
	
	
	// output
	// génère le output de l'élément
	abstract public function output():string;
	
	
	// structure
	// retourne la structure de l'élément
	abstract public function structure():array;
	
	
	// setCallback
	// attribute un tableau ou objet callback à la classe
	public function setCallback(string $key,?callable $value):self 
	{
		$this->callback[$key] = $value;
		
		return $this;
	}
	
	
	// callback
	// retourne un callback lié, si existant
	public function callback(string $key):?callable 
	{
		return (array_key_exists($key,$this->callback))? $this->callback[$key]:null;
	}
}
?>