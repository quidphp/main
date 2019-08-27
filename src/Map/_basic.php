<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/base/blob/master/LICENSE
 */

namespace Quid\Main\Map;
use Quid\Base;

// _basic
trait _basic
{
	// isIndexed
	// retourne vrai si la map est vide ou contient seulement des clés numériques
	public function isIndexed():bool
	{
		return Base\Arr::isIndexed($this->arr());
	}


	// isSequential
	// retourne vrai si la map est séquentielle
	public function isSequential():bool
	{
		return Base\Arr::isSequential($this->arr());
	}


	// isAssoc
	// retourne vrai si le tableau est vide ou associatif
	public function isAssoc():bool
	{
		return Base\Arr::isAssoc($this->arr());
	}


	// hasNumericKey
	// retourne vrai si la map contient au moins une clé numérique, retourne faux si vide
	public function hasNumericKey():bool
	{
		return Base\Arr::hasNumericKey($this->arr());
	}


	// hasNonNumericKey
	// retourne vrai si la map contient au moins une clé non numérique, retourne faux si vide
	public function hasNonNumericKey():bool
	{
		return Base\Arr::hasNonNumericKey($this->arr());
	}


	// hasKeyCaseConflict
	// retourne vrai si la map contient au moins une clé en conflit de case si insensible à la case
	public function hasKeyCaseConflict():bool
	{
		return Base\Arr::hasKeyCaseConflict($this->arr());
	}


	// isUni
	// retourne vrai si la map est vide ou unidimensionnel
	public function isUni():bool
	{
		return Base\Arr::isUni($this->arr());
	}


	// isMulti
	// retourne vrai si la map est multidimensionnel, retourne faux si vide
	public function isMulti():bool
	{
		return Base\Arr::isMulti($this->arr());
	}


	// onlyNumeric
	// retourne vrai si la map est vide ou a seulement des clés et valeurs numérique
	public function onlyNumeric():bool
	{
		return Base\Arr::onlyNumeric($this->arr());
	}


	// isSet
	// retourne vrai si la map est vide ou contient seulement des clés numériques et valeurs scalar
	public function isSet():bool
	{
		return Base\Arr::isSet($this->arr());
	}
}
?>