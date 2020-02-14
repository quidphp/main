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
// trait with methods to concatenate files and directories
trait _concatenate
{
    // configConcatenate
    public static $configConcatenate = [
        'concatenator'=>null,
        'concatenateService'=>null,
        'concatenateExtension'=>null
    ];


    // concatenateFrom
    // écrit dans le fichier js le contenu d'un ou plusieurs dossiers contenant du javascript
    // utilise la classe main/concatenator
    final public function concatenateFrom($values,?array $option=null):self
    {
        $option = Base\Arr::plus(['extension'=>$this->getAttr('concatenateExtension'),'separator'=>PHP_EOL.PHP_EOL,'concatenator'=>null],$option);

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


    // concatenateService
    // retourne la classe du service
    final protected function concatenateService():string
    {
        return $this->getAttr('concatenateService')::getOverloadClass();
    }


    // concatenateExtension
    // retourne les extensions pour la concatenation
    final public static function concatenateExtension():array
    {
        return (array) static::$config['concatenateExtension'];
    }


    // concatenateMany
    // génère plusieurs concatenations de fichiers
    final public static function concatenateMany(array $value,?array $overOption=null,?array $option=null):Main\Files
    {
        $return = Main\Files::newOverload();

        foreach ($value as $key => $array)
        {
            if(is_array($array) && !empty($array))
            {
                $array = Base\Arrs::replace($option,$array,$overOption);
                $file = static::concatenateOne($array);

                if(!empty($file))
                $return->add($file);
            }
        }

        return $return;
    }


    // shouldConcatenateOne
    // retourne vrai s'il faut lancer la concatenation pour l'array
    final public static function shouldConcatenateOne(array $array):bool
    {
        $return = false;
        $to = $array['to'] ?? null;
        $from = $array['from'] ?? null;
        $overwrite = $array['overwrite'] ?? null;

        if(is_string($to) && !empty($to) && !empty($from))
        {
            $extension = static::concatenateExtension();

            if($overwrite === true || Base\Dir::isOlderThanFrom($to,$from,true,['visible'=>true,'extension'=>$extension]))
            $return = true;
        }

        return $return;
    }


    // concatenateOne
    // passe à travers une demande de concatenation
    // si overwrite est true, écrase le fichier dans tous les cas
    // si overwrite est null, écrase le fichier seulement si la date de modifcation des sources est plus récente
    final public static function concatenateOne(array $array):?Main\File
    {
        $return = null;

        if(static::shouldConcatenateOne($array))
        {
            $to = $array['to'];
            $from = $array['from'];
            $keys = ['to','from','overwrite'];
            $option = Base\Arr::keysStrip($keys,$array);

            $return = static::newCreate($to);
            $return->concatenateFrom($from,$option);
        }

        return $return;
    }
}
?>