<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main\File;
use Quid\Base;
use Quid\Main;

// csv
// class for a csv file
class Csv extends Text implements Main\Contract\Import
{
    // config
    public static $config = [
        'group'=>'csv',
        'read'=>[ // option pour read
            'callback'=>[self::class,'readCallback'],
            'removeBom'=>true,
            'csv'=>true,
            'delimiter'=>';',
            'enclosure'=>'"',
            'escape'=>'\\'],
        'write'=>[ // option pour write
            'csv'=>true,
            'delimiter'=>';',
            'enclosure'=>'"',
            'escape'=>'\\'],
        'toUtf8'=>null, // lance la méthode utf8_encode
        'whiteSpace'=>true, // lance la méthode base/str removeWhiteSpace
        'fixUnicode'=>true, // lance méthode base/str fixUnicode
        'cast'=>true // cast les valeurs numérique
    ];


    // same
    // retourne vrai si toutes les colonnes du tableau csv ont le même count et les mêmes clés
    public function same():bool
    {
        return Base\Csv::same($this->read());
    }


    // clean
    // efface toutes les colonnes qui n'ont pas la même longueur et les mêmes clés que la première
    // si removeEmpty est true, une colonne dont toutes les valeurs sont vides est éliminé
    public function clean(bool $removeEmpty=true):array
    {
        return Base\Csv::clean($this->read(),$removeEmpty);
    }


    // assoc
    // la première colonne contient les headers
    // le nom des headers est appliqué comme clé à chaque colonne
    public function assoc(bool $clean=false,bool $removeEmpty=true):array
    {
        return Base\Csv::assoc($this->read(),$clean,$removeEmpty);
    }


    // lineReturn
    // gère la valeur de retour pour line
    // peut encoder le retour, trim chaque valeur ainsi que caster les valeurs numériques string
    // méthode protégé
    protected function lineReturn($return)
    {
        $closure = function(string $return,array $option) {
            if(!empty($option['toUtf8']))
            $return = Base\Encoding::toUtf8($return);

            if(!empty($option['fixUnicode']))
            $return = Base\Str::fixUnicode($return);

            if(!empty($option['whiteSpace']))
            $return = Base\Str::removeWhiteSpace($return);

            return $return;
        };

        $return = Base\Call::map('string',$closure,$return,$this->attr());

        $cast = $this->getAttr('cast');
        if($cast === true && is_array($return))
        $return = Base\Arrs::cast($return);

        return $return;
    }


    // sourceRewind
    // ramène le pointeur de la source au début
    public function sourceRewind():void
    {
        $this->seekRewind();

        return;
    }


    // sourceOne
    // retourne une entrée de la source
    // i agit comme référence
    public function sourceOne($offset=true,$length=true,int &$i,?array $option=null)
    {
        return $this->lineRef($offset,$length,$i,$option);
    }


    // targetInsert
    // fait une insertion sur le fichier, utilisé à partir de main/importer
    public function targetInsert(array $data,?array $option=null):bool
    {
        $return = false;

        if($this->isWritable())
        {
            $this->seekEnd();
            $this->write($data);
            $return = true;
        }

        return $return;
    }


    // targetUpdate
    // fait une mise à jour sur le fichier, utilisé à partir de main/importer
    public function targetUpdate(array $data,int $offset,?array $option=null):bool
    {
        $return = false;

        if($this->isWritable())
        {
            $this->lineSplice($offset,1,$data);
            $return = true;
        }

        return $return;
    }


    // targetDelete
    // fait une suppresion sur le fichier, utilisé à partir de main/importer
    public function targetDelete(int $offset,?array $option=null):bool
    {
        $return = false;

        if($this->isWritable())
        {
            $this->lineSplice($offset,1,null);
            $return = true;
        }

        return $return;
    }


    // targetTruncate
    // vide le fichier, utilisé à partir de main/importer
    public function targetTruncate(?array $option=null):bool
    {
        $return = false;

        if($this->isWritable())
        {
            $this->empty(0,$option);
            $return = true;
        }

        return $return;
    }


    // readCallback
    // utilisé lors du read pour un csv
    public static function readCallback($value):?array
    {
        return Base\Csv::strToArr($value,Base\Arr::gets(['delimiter','enclosure','escape'],static::$config['read']));
    }
}

// init
Csv::__init();
?>