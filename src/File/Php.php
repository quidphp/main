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

// php
// class for a php file
class Php extends Text
{
    // trait
    use _concatenate;


    // config
    protected static array $config = [
        'group'=>'php',
        'concatenateExtension'=>'php',
        'nameMatch'=>['class ','abstract class ','final class ','trait ','interface ']
    ];


    // getInfo
    // retourne un tableau d'information sur le fichier php
    // retourne le nom, namespace et type
    final public function getInfo():array
    {
        return static::infoFromArray($this->lines());
    }


    // getNamespace
    // retourne le namespace du fichier
    final public function getNamespace():?string
    {
        return $this->getInfo()['namespace'];
    }


    // getName
    // retourne le nom de la classe, du trait ou de l'interface
    final public function getName():?string
    {
        return $this->getInfo()['name'];
    }


    // innerLines
    // retourne les lignes internes du fichier php
    // enlève les tags, s'il y a un namespace met entre bracket
    final public function innerLines(?array $option=null):array
    {
        return static::innerLinesFromArray($this->lines(),$option);
    }


    // infoFromString
    // retourne les infos à partir d'une string php
    final public static function infoFromString(string $value):array
    {
        return static::infoFromArray(Base\Str::lines($value));
    }


    // infoFromArray
    // retourne les infos à partir d'un tableau de lignes php
    final public static function infoFromArray(array $lines):array
    {
        $return = [];
        $type = null;
        $namespace = null;
        $name = null;
        $nameMatch = static::$config['nameMatch'];

        foreach ($lines as $key => $value)
        {
            if(empty($namespace) && Base\Str::isStartEnd('namespace ',';',$value))
            $namespace = Base\Str::stripStartEnd('namespace ',';',$value);

            if(empty($type) && Base\Str::isStarts($nameMatch,$value))
            {
                $value = str_ireplace(['abstract class ','final class '],'class ',$value);

                $explode = Base\Str::wordExplode($value,null,true,true);
                if(!empty($explode[0]) && !empty($explode[1]))
                {
                    $type = strtolower($explode[0]);
                    $name = $explode[1];
                }

                break;
            }
        }

        $return = ['name'=>$name,'namespace'=>$namespace,'type'=>$type];

        return $return;
    }


    // innerLinesFromString
    // prend une string php et retourne la string des inner lines php
    final public static function innerLinesFromString(string $value,?array $option=null):string
    {
        $return = null;
        $lines = Base\Str::lines($value);
        $innerLines = static::innerLinesFromArray($lines,$option);
        $return = Base\Str::lineImplode($innerLines);

        return $return;
    }


    // innerLinesFromArray
    // prend un tableau de lignes php et retourne un tableau inner lines php
    final public static function innerLinesFromArray(array $lines,?array $option=null):array
    {
        $return = [];
        $option = Base\Arr::plus(['emptyLine'=>false,'comment'=>false,'initMethod'=>null,'closure'=>false,'return'=>true],$option);
        $info = static::infoFromArray($lines);
        $hasNamespace = !empty($info['namespace']);
        $hasName = !empty($info['name']);
        $isClass = ($info['type'] === 'class');
        $foundNamespace = false;
        $hasClosure = false;
        $inComment = false;
        $nameMatch = static::$config['nameMatch'];

        foreach ($lines as $key => $value)
        {
            $valueNoWs = Base\Str::removeWhitespace($value);

            if(in_array($valueNoWs,['<?php','?>'],true))
            continue;

            if(Base\Str::isStartEnd('declare(',';',$valueNoWs))
            continue;

            if($option['comment'] === false && Base\Str::isStart('// ',$valueNoWs))
            continue;

            if($option['return'] === false && Base\Str::isStart('return ',$value))
            continue;

            if($valueNoWs === '' && ($option['emptyLine'] === false || ($hasNamespace === true && $foundNamespace === false)))
            continue;

            if(Base\Str::isStartEnd('namespace ',';',$valueNoWs))
            {
                $namespace = Base\Str::stripStartEnd('namespace ',';',$valueNoWs);
                $value = "namespace $namespace {";
                $foundNamespace = true;
            }

            if($option['closure'] === true && $hasNamespace === true && $hasName === true && Base\Str::isStarts($nameMatch,$value))
            {
                $return[] = '\Quid\Main\Autoload::setClosure("'.$info['namespace'].'","'.$info['name'].'",function() {';
                $hasClosure = true;
            }

            if(Base\Str::isStart('/*',$valueNoWs))
            $inComment = true;

            if($option['comment'] === true || $inComment === false)
            $return[] = $value;

            if(Base\Str::isEnd('*/',$valueNoWs))
            $inComment = false;
        }

        if($hasNamespace === true && $foundNamespace === true)
        {
            if(is_string($option['initMethod']) && $hasName === true && $isClass === true)
            {
                $initMethod = $info['name'].'::'.$option['initMethod'].'();';
                $return[] = $initMethod;
            }

            if($hasClosure === true)
            $return[] = '});';

            $return[] = '}';
        }

        return $return;
    }
}

// init
Php::__init();
?>