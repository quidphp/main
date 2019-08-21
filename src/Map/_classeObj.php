<?php
declare(strict_types=1);
namespace Quid\Main\Map;
use Quid\Main;
use Quid\Base;

// _classeObj
trait _classeObj
{
	// trait
	use _filter;
	
	
	// classeOrObj
	// retourne si le trait doit utilisé l'appelation de classe :: ou d'objet ->
	abstract public static function classeOrObj():string;
	
	
	// onPrepareThis
	// retourne l'objet cloner pour certaines méthodes
	public function onPrepareThis(string $method):Main\Map
	{
		return (\in_array($method,['filter','sortBy'],true))? $this->clone():$this;
	}
	
	
	// not
	// clone l'objet et enlève les valeurs données via unset
	public function not(...$values) 
	{
		return $this->clone()->unset(...$values);
	}
	
	
	// pair 
	// retourne un tableau associatif avec le clé et le résultat d'une méthode
	public function pair(string $method,...$args):array 
	{
		$return = [];
		$type = static::classeOrObj();
		
		foreach ($this->arr() as $key => $value) 
		{
			if($type === 'obj')
			$return[$key] = $value->$method(...$args);
			
			else
			$return[$key] = $value::$method(...$args);
		}
		
		return $return;
	}
	
	
	// pairStr
	// retourne une string avec le résultat combiné d'une méthode
	// exception envoyé si résultat d'une méthode non scalaire et non null
	public function pairStr(string $method,...$args):string 
	{
		$return = '';
		$type = static::classeOrObj();
		
		foreach ($this->arr() as $key => $value) 
		{
			if($type === 'obj')
			$str = $value->$method(...$args);
			else
			$str = $value::$method(...$args);
			
			if(\is_scalar($str) || $str === null)
			$return .= $str;
			
			else
			static::throw('invalidReturnValue');
		}
		
		return $return;
	}
	
	
	// filterCondition
	// utilisé par les méthodes comme filter pour vérifier si une entrée respecte une condition
	// méthode protégé
	protected function filterCondition($condition,$key,$value,...$args):bool
	{
		$return = true;
		$type = static::classeOrObj();
		
		if(static::classIsCallable($condition))
		$return = (Base\Call::withObj($this,$condition,$value,$key,...$args) === true)? true:false;
		
		elseif(\is_array($condition))
		{
			foreach ($condition as $k => $v) 
			{
				$return = false;
				if(\is_string($k))
				{
					if($type === 'obj')
					{
						if($value->$k(...$args) === $v)
						$return = true;
					}
					
					elseif($value::$k(...$args) === $v)
					$return = true;
				}
				
				if($return === false)
				break;
			}
		}
		
		return $return;
	}
	
	
	// group
	// permet de grouper le contenu en multiples classes ou objets groupé par la valeur d'un résultat de méthode
	// la valeur doit être key, donc null et booléean envoie une exception
	// retourne un tableau avec des classes ou objets
	public function group(string $method,...$args):array 
	{
		$return = [];
		$type = static::classeOrObj();
		
		foreach ($this->arr() as $key => $value) 
		{
			if($type === 'obj')
			$k = $value->$method(...$args);
			else
			$k = $value::$method(...$args);
			
			if(\is_object($k))
			$k = Base\Obj::cast($k);
			
			if(Base\Arr::isKey($k))
			{
				if(!\array_key_exists($k,$return))
				$return[$k] = new static();
				
				$data =& $return[$k]->arr();
				$data[$key] = $value;
			}
			
			else
			static::throw('notAnArrayKey',$value);
		}
		
		return $return;
	}
	
	
	// sortBy
	// sort l'objet par le résultat d'une méthode
	// clone l'objet
	public function sortBy(string $method,bool $sort=true,...$args):self 
	{
		$this->checkAllowed('sort');
		$return = $this->onPrepareThis('sortBy');
		$type = static::classeOrObj();
		$data =& $return->arr();
		
		if($type === 'obj')
		$data = Base\Obj::sort($method,$sort,$data,...$args);
		else
		$data = Base\Classe::sort($method,$sort,$data,...$args);
		
		return $return->checkAfter();
	}
	
	
	// sortDefault
	// ramène l'objet dans son sort naturel
	// la méthode doit être défini dans la propriété static sortDefault
	// ne clone pas l'objet
	public function sortDefault():self
	{
		$this->checkAllowed('sort');
		$return = $this->onPrepareThis('sort');
		$type = static::classeOrObj();
		$data =& $return->arr();
		
		if($type === 'obj')
		$data = Base\Obj::sort(static::$sortDefault,true,$data);
		else
		$data = Base\Classe::sort(static::$sortDefault,true,$data);
		
		return $return->checkAfter();
	}
}
?>