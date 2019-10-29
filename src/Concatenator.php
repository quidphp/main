<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main;
use Quid\Base;

// concatenator
// class used to concatenate the content of many files or directories
class Concatenator extends Map
{
    // trait
    use _option;


    // config
    public static $config = [
        'option'=>[
            'empty'=>true, // si le fichier est effacé au lancement du trigger
            'start'=>null, // contenu à mettre en début de rendu, peut être une callable
            'end'=>null, // contenu à mettre en fin de rendu, peut être une callable
            'separator'=>PHP_EOL, // séparateur entre entrée
            'callable'=>null, // permet de spécifier un callable en fin de trigger
            'entry'=>[
                'start'=>null, // contenu à mettre en début d'entrée, peut être une callable
                'end'=>null, // contenu à mettre en fin d'entrée, peut être une callable
                'lineStart'=>null, // ligne de départ pour les fichiers dans l'entrée
                'lineEnd'=>null, // ligne de fin pour les fichiers dans l'entrée
                'content'=>null, // callable pour le contenu d'un fichier avant écriture
                'separator'=>PHP_EOL, // séparateur entre chaque fichier de l'entrée
                'extension'=>null, // extension pour les fichiers
                'dig'=>true, // s'il faut creuser dans le dossier
                'remove'=>null, // si c'est un dossier, permet d'exclure des classes
                'priority'=>null]] // priority si c'est un dossier, permet de mettre des fichiers en avant
    ];


    // map
    protected static $allow = ['unset','serialize','clone']; // méthodes permises


    // construct
    // construit l'objet de phpConcatenator
    public function __construct(?array $option=null)
    {
        $this->option($option);

        return;
    }


    // add
    // ajoute une entrée au concatenateur
    // chaque entrée peut avoir ses propres options
    public function add($value,?array $option=null):self
    {
        if(!empty($value))
        {
            $data =& $this->arr();
            $array = [$value,$option];
            $data[] = $array;
        }

        else
        static::throw('valueCannotBeEmpty');

        return $this;
    }


    // addStr
    // ajoute une entrée string au concatenateur, ceci sera wrapper dans une closure
    // chaque entrée peut avoir ses propres options
    public function addStr(string $value,?array $option=null):self
    {
        return $this->add(function() use($value) { return $value; },$option);
    }


    // parse
    // retourne un tableau avec toutes les entrées parse
    // est utilisé lors du trigger
    public function parse():array
    {
        $return = [];

        foreach ($this->arr() as $array)
        {
            $prepare = $this->prepareEntry(...$array);

            if(!empty($prepare))
            $return[] = $prepare;
        }

        return $return;
    }


    // prepareEntry
    // permet de préparer une entrée de l'objet concatenateur
    // retourne un tableau avec deux éléments
    // méthode protégé
    protected function prepareEntry($value,?array $option=null):?array
    {
        $return = null;
        $option = Base\Arr::plus($this->getOption('entry'),$option);

        if($value instanceof \Closure)
        $values = [$value];
        else
        $values = $this->getEntryFiles($value,$option);

        if(!empty($values))
        $return = [$values,$option];

        return $return;
    }


    // getEntryFiles
    // retourne un tableau avec tous les fichiers d'une entrée du concatenateur
    // méthode protégé
    protected function getEntryFiles($value,array $option):array
    {
        $return = [];
        
        if($value instanceof File)
        $value = $value->path();

        if(is_string($value))
        {
            $value = Base\Finder::normalize($value);

            if(Base\File::is($value))
            {
                if(empty($option['extension']) || Base\Path::isExtension($option['extension'],$value))
                $return[] = $value;
            }

            elseif(Base\Dir::is($value))
            {
                $in = ['type'=>'file'];
                if(!empty($option['extension']))
                $in['extension'] = $option['extension'];

                $return = Base\Dir::getVisible($value,$option['dig'],['in'=>$in]);

                if(!empty($return))
                {
                    if(!empty($option['remove']))
                    $return = Base\Dir::remove($return,$option['remove'],$value);

                    if(!empty($option['priority']))
                    $return = Base\Dir::sortPriority($return,$option['priority'],$value);
                }
            }
        }
        
        return $return;
    }


    // trigger
    // lance la compilation de tous les entrées liés à l'objet
    // retourne la string concatene
    public function trigger():string
    {
        $return = '';
        $start = $this->getOptionCall('start');
        $end = $this->getOptionCall('end');
        $separator = $this->getOption('separator');
        $callable = $this->getOption('callable');
        $return = '';

        if(is_string($start))
        $return .= $start;
        
        $int = 0;
        foreach ($this->parse() as $entry)
        {
            $content = $this->makeEntry(...$entry);

            if(strlen($content))
            {
                if(is_string($separator) && $int > 0)
                $return .= $separator;

                $return .= $content;
                $int++;
            }
        }

        if(is_string($end))
        $return .= $end;

        if(static::classIsCallable($callable))
        $return = $callable($return);
        
        return $return;
    }


    // triggerWrite
    // trigger et écrit dans le fichier donné en argument
    public function triggerWrite($value):File
    {
        $return = File::newCreate($value);

        if($this->getOption('empty') === true)
        $return->empty();

        $write = $this->trigger();
        $return->write($write);

        return $return;
    }


    // makeEntry
    // génère le contenu d'une entrée sous forme de string
    // méthode protégé
    protected function makeEntry(array $values,array $option):?string
    {
        $return = '';
        $separator = $option['separator'];
        $start = (static::classIsCallable($option['start']))? $option['start']($option):$option['start'];
        $end = (static::classIsCallable($option['end']))? $option['end']($option):$option['end'];

        if(is_string($start))
        $return .= $start;

        $str = '';
        foreach ($values as $value)
        {
            if($value instanceof \Closure)
            $content = $value();

            else
            $content = Base\File::read(true,true,$value);

            if(is_string($content))
            {
                $content = $this->prepareEntryFile($content,$option);

                if(strlen($str) && is_string($separator))
                $str .= $separator;

                $str .= $content;
            }

            else
            static::throw('unreadable',$value);
        }
        $return .= $str;

        if(is_string($end))
        $return .= $end;

        return $return;
    }


    // prepareEntryFile
    // génère le contenu d'un fichier d'une entrée sous forme de string
    // méthode protégé
    protected function prepareEntryFile(string $return,array $option):string
    {
        $lineStart = $option['lineStart'];
        $lineEnd = $option['lineEnd'];
        $content = $option['content'];

        if(is_int($lineStart))
        $return = Base\Str::lineSplice(0,$lineStart,null,$return);

        if(is_string($return) && is_int($lineEnd))
        $return = Base\Str::lineSplice(-$lineEnd,$lineEnd,null,$return);

        if(is_string($return) && static::classIsCallable($content))
        $return = $content($return);

        return $return;
    }
}
?>