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

// timeout
// class for a collection containing timeout data (to deny an action if it already has happened too much)
class Timeout extends Map
{
    // config
    public static array $config = [
        'max'=>1, // après combien de tentative le timeout est déclenché
        'timeout'=>600 // durée du timeout
    ];


    // dynamique
    protected ?array $mapAllow = ['set','unset','serialize','empty']; // méthodes permises


    // isTimedOut
    // retourne vrai si l'entrée dans l'objet est en timeout
    final public function isTimedOut($key):bool
    {
        return $this->getExpire($key) !== null;
    }


    // isMaxed
    // retourne vrai si le compte de l'entrée est au maximum autorisé
    final public function isMaxed($key):bool
    {
        $return = false;
        $value = $this->get($key);

        if(is_array($value))
        {
            if(is_int($value['count']) && is_int($value['max']) && $value['count'] >= $value['max'])
            $return = true;
        }

        return $return;
    }


    // set
    // permet d'ajouter ou modifier une entrée dans l'objet
    final public function set($key,$value):self
    {
        return $this->change($key,$value,false);
    }


    // change
    // permet de changer une entrée en conservant les valeurs existantes
    // n'envoie pas d'exception si l'entrée n'existe pas
    final public function change($key,$value,bool $merge=true):self
    {
        $key = $this->onPrepareKey($key);
        $current = null;

        if(!is_string($key))
        static::throw('invalidKey');

        if(!is_array($value) && $value !== null)
        static::throw('invalidValue');

        if($merge === true)
        $current = $this->get($key);

        if($current === null)
        $current = $this->attr();

        $value = Base\Arr::replace($current,$value);
        $value = static::checkValueValid($value);
        parent::set($key,$value);

        return $this;
    }


    // changes
    // permet de changer plusieurs entrées en conservant les valeurs existantes
    // n'envoie pas d'exception si l'entrée n'existe pas
    final public function changes(array $keyValue):self
    {
        foreach ($keyValue as $key => $value)
        {
            $this->change($key,$value,true);
        }

        return $this;
    }


    // getCount
    // retourne le compte actuelle de l'entrée
    // si resetOnTimeout est vrai, si l'entrée n'est plus en timeout le compte est ramené à zéro avant le retour
    final public function getCount($key,bool $resetOnTimeout=true):?int
    {
        $return = null;
        $value = $this->get($key);

        if(is_array($value))
        {
            if($resetOnTimeout === true)
            {
                $this->resetOne($key);
                $value = $this->get($key);
            }

            $return = $value['count'];
        }

        return $return;
    }


    // setCount
    // permet de changer le count actuelle de l'entrée
    // si count est null, met le max
    // met le timestamp actuel
    final public function setCount($key,?int $count=null,?int $timestamp=null):self
    {
        $this->checkExists($key);
        $this->resetOne($key);
        $data =& $this->arr();
        $key = $this->onPrepareKey($key);

        $count = ($count === null)? $data[$key]['max']:$count;
        $data[$key]['count'] = $count;
        $this->setTimestamp($key,$timestamp);

        return $this;
    }


    // block
    // similaire à setCount, count est mis au max
    final public function block($key):self
    {
        return $this->setCount($key);
    }


    // addCount
    // permet d'ajouter ou incrémenter un count à l'entrée
    // met le timestamp actuel
    final public function addCount($key,int $count=1,?int $timestamp=null):self
    {
        $this->checkExists($key);
        $this->resetOne($key);
        $data =& $this->arr();
        $key = $this->onPrepareKey($key);

        $data[$key]['count'] += $count;
        $this->setTimestamp($key,$timestamp);

        return $this;
    }


    // increment
    // similaire à addCount, count ne peut être que 1
    final public function increment($key):self
    {
        return $this->addCount($key);
    }


    // resetCount
    // ramène le count et le timestamp de l'entrée à l'état initial
    final public function resetCount($key):self
    {
        $this->checkExists($key);
        $data =& $this->arr();
        $key = $this->onPrepareKey($key);

        $data[$key]['count'] = 0;
        $this->resetTimestamp($key);

        return $this;
    }


    // getTimestamp
    // retourne le timestamp courant de l'entrée
    // si resetOnTimeout est vrai, si l'entrée n'est plus en timeout le compte est ramené à zéro avant le retour du timestamp null
    final public function getTimestamp($key,bool $resetOnTimeout=true):?int
    {
        $value = $this->get($key);

        if(is_array($value))
        {
            if($resetOnTimeout === true)
            {
                $this->resetOne($key);
                $value = $this->get($key);
            }

            $return = $value['timestamp'];
        }

        return $return;
    }


    // setTimestamp
    // change le timestamp pour une entrée
    final public function setTimestamp($key,?int $timestamp=null):self
    {
        $this->checkExists($key);
        $data =& $this->arr();
        $key = $this->onPrepareKey($key);

        if(array_key_exists($key,$data))
        {
            $timestamp = (is_int($timestamp))? $timestamp:Base\Datetime::now();
            $data[$key]['timestamp'] = $timestamp;
        }

        return $this;
    }


    // resetTimestamp
    // reset le timestamp pour une entrée
    final public function resetTimestamp($key):self
    {
        $this->checkExists($key);
        $data =& $this->arr();
        $key = $this->onPrepareKey($key);

        if(array_key_exists($key,$data))
        $data[$key]['timestamp'] = null;

        return $this;
    }


    // getExpire
    // retourne le nombre de secondes avant que le timeout sur l'entrée soit expiré
    // retourne null si l'entrée n'est pas en timeout
    final public function getExpire($key):?int
    {
        $return = null;
        $value = $this->get($key);

        if(is_array($value) && $this->isMaxed($key))
        {
            if(is_int($value['timeout']) && is_int($value['timestamp']))
            {
                $current = Base\Datetime::now();
                $expire = ($value['timestamp'] + $value['timeout']) - $current;

                if($expire > 0)
                $return = $expire;
            }
        }

        return $return;
    }


    // resetOne
    // si l'entrée est maxed mais plus en timeout, envoie dans resetCount
    final public function resetOne($key):self
    {
        if($this->isMaxed($key) && !$this->isTimedOut($key))
        $this->resetCount($key);

        return $this;
    }


    // resetAll
    // passe chaque entrée dans la méthode resetOne
    final public function resetAll():self
    {
        foreach ($this->keys() as $key)
        {
            $this->resetOne($key);
        }

        return $this;
    }


    // checkValueValid
    // met une valeur par défaut pour count et timestamp
    // envoie une exception si le tableau value est invalid
    final public static function checkValueValid(array $return):array
    {
        $return['count'] = (empty($return['count']))? 0:$return['count'];
        $return['timestamp'] = (empty($return['timestamp']))? null:$return['timestamp'];

        if(array_key_exists('max',$return) && array_key_exists('timeout',$return))
        {
            if(!is_int($return['max']) || $return['max'] <= 0)
            static::throw($return['max'],'invalidMax');

            if(!is_int($return['timeout']) || $return['timeout'] <= 0)
            static::throw($return['timeout'],'invalidTimeout');
        }

        else
        static::throw('invalidArray');

        return $return;
    }
}
?>