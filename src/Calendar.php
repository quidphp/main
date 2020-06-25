<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main;
use Quid\Base;

// calendar
// class that provides logic for the calendar widget
class Calendar extends Widget
{
    // config
    protected static array $config = [
        'formatCurrent'=>'calendar', // format de date pour le mois
        'head'=>'head',
        'current'=>'current',
        'body'=>'body'
    ];


    // dynamique
    protected int $timestamp; // contient le timestamp du mois à afficher
    protected ?string $format = null; // permet de storer un format à mettre dans les attributs de td
    protected ?array $selected = null; // contient les timestamp sélectionnés


    // construct
    // construit l'objet calendar
    final public function __construct($value=null,?array $attr=null)
    {
        $this->makeAttr($attr);
        $this->setTimestamp($value);
    }


    // cast
    // pour cast, retourne le timestamp du calendrier
    final public function _cast():int
    {
        return $this->timestamp();
    }


    // setTimestamp
    // change le timestamp de l'objet
    // exception envoyé si ce n'est pas un int
    final public function setTimestamp($value=null):self
    {
        $value = Base\Datetime::time($value);

        if(is_int($value))
        $this->timestamp = Base\Datetime::floorMonth($value);

        if(!is_int($this->timestamp))
        static::throw('invalidTimestamp');

        return $this;
    }


    // timestamp
    // retourne le timestamp de l'objet calendar
    final public function timestamp():int
    {
        return $this->timestamp;
    }


    // prevTimestamp
    // retourne le timestamp du mois précédent
    final public function prevTimestamp():int
    {
        return Base\Datetime::addMonth(-1,$this->timestamp());
    }


    // nextTimestamp
    // retourne le timestamp du mois suivant
    final public function nextTimestamp():int
    {
        return Base\Datetime::addMonth(1,$this->timestamp());
    }


    // parseTimestamp
    // retourne un tableau avec les attributs d'un timestamp
    // peut être today, in et out
    // peut être utilisé comme attribut html
    final public function parseTimestamp(int $value):array
    {
        $return = [];
        $value = Base\Datetime::floorDay($value);
        $format = $this->format();

        if(Base\Datetime::isToday($value))
        $return[] = 'today';

        if(Base\Datetime::isWeekend($value))
        $return[] = 'weekend';

        if(Base\Datetime::isMonth($value,null,$this->timestamp()))
        $return[] = 'in';

        else
        $return[] = 'out';

        if($this->isSelected($value))
        $return[] = 'selected';

        $return['data-timestamp'] = $value;

        if(is_string($format))
        $return['data-format'] = Base\Datetime::format($format,$value);

        return $return;
    }


    // setFormat
    // permet de lier un format à mettre dans les attributs de colonne
    final public function setFormat(?string $value):self
    {
        $this->format = $value;

        return $this;
    }


    // format
    // retourne le format lié
    final public function format():?string
    {
        return $this->format;
    }


    // isSelected
    // retourne vrai si le timestamp est dans une journée sélectionné
    final public function isSelected(int $value):bool
    {
        $return = false;
        $selected = $this->selected();

        if(is_array($selected))
        {
            foreach ($selected as $v)
            {
                if(is_numeric($v) && Base\Datetime::isDay($v,null,$value))
                {
                    $return = true;
                    break;
                }
            }
        }

        return $return;
    }


    // setSelected
    // permet de mettre un ou plusieurs timestamp comme sélectionné
    final public function setSelected($value):self
    {
        if(is_numeric($value))
        $value = [$value];

        if(is_array($value) && !empty($value))
        $this->selected = Base\Arr::cast($value);

        return $this;
    }


    // selected
    // retourne les timestamp sélectionnés
    final public function selected():?array
    {
        return $this->selected;
    }


    // structure
    // retourne la structure du calendrier
    final public function structure():array
    {
        return Base\Datetime::calendar($this->timestamp(),true,true);
    }


    // output
    // génère le calendrier en html
    final public function output():string
    {
        $return = $this->head();
        $return .= $this->body();

        return $return;
    }


    // head
    // génère la partie supérieure du calendrier
    final protected function head():string
    {
        $return = Base\Html::divOp($this->getAttr('head'));
        $timestamp = $this->timestamp();

        $callback = $this->callback('prev');
        if(!empty($callback))
        {
            $prevTimestamp = $this->prevTimestamp();
            $return .= $callback($prevTimestamp,$this);
        }

        $formatCurrent = $this->getAttr('formatCurrent');
        if(!empty($formatCurrent))
        {
            $return .= Base\Html::divOp($this->getAttr('current'));
            $return .= Base\Datetime::format($formatCurrent,$timestamp);
            $return .= Base\Html::divCl();
        }

        $callback = $this->callback('next');
        if(!empty($callback))
        {
            $nextTimestamp = $this->nextTimestamp();
            $return .= $callback($nextTimestamp,$this);
        }

        $return .= Base\Html::divCl();

        return $return;
    }


    // body
    // génère la table du calendrier
    final protected function body():string
    {
        $return = Base\Html::divOp($this->getAttr('body'));
        $return .= Base\Html::tableOp();
        $return .= $this->tableHead();
        $return .= $this->tableBody();
        $return .= Base\Html::tableCl();
        $return .= Base\Html::divCl();

        return $return;
    }


    // tableHead
    // génère le thead de la table du calendrier
    final protected function tableHead():string
    {
        $return = '';
        $ths = [];
        $daysShort = Base\Datetime::getDaysShort();

        if(!empty($daysShort) && count($daysShort) === 7)
        {
            foreach ($daysShort as $value)
            {
                $span = Base\Html::span($value);
                $ths[] = [$span];
            }
        }

        $return = Base\Html::thead($ths);

        return $return;
    }


    // tableBody
    // génère le tbody de la table du calendrier
    final protected function tableBody():string
    {
        $return = '';
        $structure = $this->structure();
        $callback = $this->callback('day');
        $trs = [];

        foreach ($structure as $weekNo => $weekDays)
        {
            if(is_array($weekDays))
            {
                $tds = [];

                foreach ($weekDays as $timestamp)
                {
                    if(is_int($timestamp))
                    {
                        $day = Base\Datetime::day($timestamp);

                        if(is_int($day))
                        {
                            $attr = $this->parseTimestamp($timestamp);

                            if(!empty($callback))
                            {
                                $td = $callback($day,$timestamp,$attr,$this);
                                if(!is_array($td))
                                $td = [$td,$attr];
                            }

                            else
                            {
                                $span = Base\Html::span($day);
                                $td = [$span,$attr];
                            }

                            if(is_array($td))
                            $tds[] = $td;
                        }
                    }
                }

                if(!empty($tds))
                $trs[] = [$tds];
            }
        }

        $return = Base\Html::tbody(...$trs);

        return $return;
    }
}

// init
Calendar::__init();
?>