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

// services
// class for a collection containing many service objects
class Services extends MapObj
{
    // trait
    use _inst;
    use Map\_sort;
    use Map\_readOnly;


    // config
    public static array $config = [];


    // dynamique
    protected ?array $mapAllow = ['set','unset','remove','sort','clone']; // méthodes permises
    protected $mapIs = Service::class; // classe d'objet permis
    protected ?string $mapSortDefault = 'getServiceKey'; // défini la méthode pour sort par défaut


    // construct
    // construit l'objet services
    final public function __construct(?array $value=null)
    {
        if(is_array($value))
        {
            $this->sets($value);
            $this->sortDefault();
        }

        return;
    }


    // set
    // ajoute un rôle à l'objet roles
    // le init config de role est lancé avant l'ajout
    final public function set($key,$value):self
    {
        if(!is_string($key))
        static::throw('onlyStringKeyAllowed');

        if($this->exists($key))
        static::throw('alreadyIn',$key);

        $class = null;
        $args = [];

        if(is_string($value))
        $class = $value;

        elseif(is_array($value))
        {
            $class = current($value);
            $args = Base\Arr::spliceFirst($value);
            $args = array_values($args);
        }

        if(!is_string($class) || !is_subclass_of($class,Service::class,true))
        static::throw('notSubClassOfService',$class);

        $value = new $class(...$args);
        $value->setServiceKey($key);
        parent::set($key,$value);

        return $this;
    }


    // pairCastClean
    // retourne un tableau avec les valeurs de cast clean
    // donc les valeurs vides ne sont pas retournés
    final public function pairCastClean():array
    {
        return Base\Arr::clean($this->pair('cast'));
    }
}
?>