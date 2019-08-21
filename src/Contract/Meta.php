<?php
declare(strict_types=1);
namespace Quid\Main\Contract;

// meta
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
	
	
	// getBodyClass
	// retourne les données les classes de body
	public function getBodyClass($value=null);
	
	
	// getBodyStyle
	// retourne les données pour les styles de body
	public function getBodyStyle($value=null);
}
?>