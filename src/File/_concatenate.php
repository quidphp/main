<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 * Readme: https://github.com/quidphp/main/blob/master/README.md
 */

namespace Quid\Main\File;
use Quid\Base;
use Quid\Main;

// _concatenate
// trait with methods to concatenate files and directories to the target
trait _concatenate
{
    // configConcatenate
    public static $configConcatenate = array(
        'service'=>null,
        'concatenator'=>null,
        'extension'=>null
    );
    
    
    // concatenateFrom
    // écrit dans le fichier js le contenu d'un ou plusieurs dossiers contenant du javascript
    // utilise la classe main/concatenator
    final public function concatenateFrom($values,?array $option=null):self
    {
        $option = Base\Arr::plus(['extension'=>$this->getAttr('extension'),'separator'=>PHP_EOL.PHP_EOL,'concatenator'=>null],$option);
        
        if(!is_array($values))
        $values = (array) $values;
        ksort($values);
        
        $concatenatorOption = $this->getConcatenatorOption($values,$option);
        $concatenator = Main\Concatenator::newOverload($concatenatorOption);

        foreach ($values as $value)
        {
            if(!is_string($value) || Base\Finder::is($value))
            $concatenator->add($value,$option);
        }
        
        $concatenator->triggerWrite($this);
        
        return $this;
    }

    
    // getConcatenatorOption
    // retourne les options pour le concatenateur
    protected function getConcatenatorOption(array $values,array $option):?array
    {
        return Base\Arrs::replace($this->getAttr('concatenator'),$option['concatenator'] ?? null);
    }
    
    
    // getServiceClass
    // retourne la classe du service
    final protected function getServiceClass():string
    {
        return $this->getAttr('service')::getOverloadClass();
    }
    
    
    // concatenateExtension
    // retourne les extensions pour la concatenation
    final public static function concatenateExtension():array 
    {
        return (array) static::$config['extension'];
    }
}
?>