<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/base/blob/master/LICENSE
 */

namespace Quid\Main\Map;
use Quid\Main;
use Quid\Base;

// _arrs
trait _arrs
{
	// onPrepareKey
	// prépare une clé pour une méthode comme get et slice
	// ne change pas les array
	protected function onPrepareKey($return)
	{
		if($return instanceof self)
		$return = $return;

		elseif(!is_scalar($return))
		$return = Base\Obj::cast($return);

		return $return;
	}


	// onPrepareReplace
	// méthode appelé avec le contenu des méthodes de remplacement
	// si le tableau de retour est unidimensionnel, passe dans sets pour que les clés avec un slash devinnent un tableau multidimensionnel
	protected function onPrepareReplace($return)
	{
		$return = parent::onPrepareReplace($return);

		if(Base\Arr::isUni($return))
		$return = Base\Arrs::sets($return,[]);

		return $return;
	}


	// exists
	// version arrs de exists
	public function exists(...$keys):bool
	{
		return Base\Arrs::keysExists($this->prepareKeys(...$keys),$this->arr(),static::isSensitive());
	}


	// in
	// version arrs de in
	public function in(...$values):bool
	{
		return Base\Arrs::ins($this->prepareValues(...$values),$this->arr(),static::isSensitive());
	}


	// keys
	// version arrs de keys
	public function keys($value=null):array
	{
		return Base\Arrs::keys($this->arr(),$this->onPrepareValue($value),static::isSensitive());
	}


	// search
	// version arrs de search
	public function search($value)
	{
		return Base\Arrs::search($this->onPrepareValue($value),$this->arr(),static::isSensitive());
	}


	// values
	// version arrs de values
	public function values($is=null):array
	{
		return Base\Arrs::values($this->arr(),$is);
	}


	// get
	// version arrs de get
	public function get($key)
	{
		return $this->onPrepareReturn(Base\Arrs::get($this->onPrepareKey($key),$this->arr(),static::isSensitive()));
	}


	// gets
	// version arrs de gets
	public function gets(...$keys)
	{
		return $this->onPrepareReturns(Base\Arrs::gets($this->prepareKeys(...$keys),$this->arr(),static::isSensitive()));
	}


	// index
	// version arrs de index
	public function index($index)
	{
		return $this->onPrepareReturn(Base\Arrs::index($index,$this->arr()));
	}


	// indexes
	// version arrs de indexes
	public function indexes(...$indexes)
	{
		return $this->onPrepareReturns(Base\Arrs::indexes($indexes,$this->arr()));
	}


	// set
	// version arrs de set
	public function set($key,$value):Main\Map
	{
		$this->checkAllowed('set')->checkBefore(false,$value);
		$return = $this->onPrepareThis('set');
		$key = $this->onPrepareKey($key);
		$value = $this->onPrepareValueSet($value);
		Base\Arrs::setRef($key,$value,$return->arr(),static::isSensitive());

		return $return->checkAfter();
	}


	// unset
	// version arrs de unset
	public function unset(...$keys):Main\Map
	{
		$this->checkAllowed('unset');
		$return = $this->onPrepareThis('unset');
		Base\Arrs::unsetsRef($return->prepareKeys(...$keys),$return->arr(),static::isSensitive());

		return $return->checkAfter();
	}


	// replace
	// version arrs de replace
	// les valeurs doivent toutes être des tableaux après prepareReplaces
	public function replace(...$values):Main\Map
	{
		$this->checkAllowed('replace');
		$return = $this->onPrepareThis('replace');
		$values = $return->prepareReplaces(...$values);
		$return->checkBefore(true,...$values);

		if(Base\Arr::validate('array',$values))
		{
			$data =& $return->arr();
			$data = Base\Arrs::replace($data,...$values);
		}

		else
		static::throw('requireArray');

		return $this->checkAfter();
	}


	// remove
	// version arrs de remove
	public function remove(...$values):Main\Map
	{
		$this->checkAllowed('remove');
		$return = $this->onPrepareThis('remove');
		$data =& $return->arr();
		$data = Base\Arrs::valuesStrip($return->prepareValues(...$values),$data,static::isSensitive());

		return $return->checkAfter();
	}


	// sort
	// version arrs de keysSort
	public function sort($sort=true,int $type=SORT_FLAG_CASE | SORT_NATURAL):Main\Map
	{
		$this->checkAllowed('sort');
		$return = $this->onPrepareThis('sort');
		$data =& $return->arr();
		$data = Base\Arrs::keysSort($data,$sort,$type);

		return $return->checkAfter();
	}


	// sequential
	// version arrs de keysSequential
	public function sequential():Main\Map
	{
		$this->checkAllowed('sequential');
		$return = $this->onPrepareThis('sequential');
		$data =& $return->arr();
		$data = Base\Arrs::values($data);

		return $return->checkAfter();
	}
}
?>