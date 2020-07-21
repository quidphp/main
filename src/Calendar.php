<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main;
use Quid\Base;
use Quid\Base\Html;

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
        $return = Base\Arr::some($selected,fn($v) => (is_numeric($v) && Base\Datetime::isDay($v,null,$value)));

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
        return $this->head().$this->body();
    }


    // head
    // génère la partie supérieure du calendrier
    final protected function head():string
    {
        $return = '';
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
            $format = Base\Datetime::format($formatCurrent,$timestamp);
            $return .= Html::div($format,$this->getAttr('current'));
        }

        $callback = $this->callback('next');
        if(!empty($callback))
        {
            $nextTimestamp = $this->nextTimestamp();
            $return .= $callback($nextTimestamp,$this);
        }

        return Html::div($return,$this->getAttr('head'));
    }


    // body
    // génère la table du calendrier
    final protected function body():string
    {
        $r = '';
        $head = $this->tableHead();
        $body = $this->tableBody();
        $r .= Html::table([$head],$body);

        return Html::div($r,$this->getAttr('body'));
    }


    // tableHead
    // génère le tableau du thead de la table du calendrier
    final protected function tableHead():array
    {
        $return = [];
        $daysShort = Base\Datetime::getDaysShort();

        if(!empty($daysShort) && count($daysShort) === 7)
        {
            foreach ($daysShort as $value)
            {
                $span = Html::span($value);
                $return[] = [$span];
            }
        }

        return $return;
    }


    // tableBody
    // génère le tableau du tbody de la table du calendrier
    final protected function tableBody():array
    {
        $return = [];
        $structure = $this->structure();
        $callback = $this->callback('day');

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
                                $span = Html::span($day);
                                $td = [$span,$attr];
                            }

                            if(is_array($td))
                            $tds[] = $td;
                        }
                    }
                }

                if(!empty($tds))
                $return[] = [$tds];
            }
        }

        return $return;
    }
}

// init
Calendar::__init();
?>