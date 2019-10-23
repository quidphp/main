<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main\File;
use Quid\Base;

// calendar
// class for a calendar file (like ics)
class Calendar extends Text
{
    // config
    public static $config = [
        'group'=>'calendar',
        'model'=>'BEGIN:VCALENDAR
		VERSION:2.0
		CALSCALE:GREGORIAN
		METHOD:PUBLISH
		BEGIN:VEVENT
		DTSTART;TZID=%timezone%:%dateStart%
		DTEND;TZID=%timezone%:%dateEnd%
		SUMMARY:%name%
		DESCRIPTION:%description%
		LOCATION:%location%
		URL:%uri%
		UID:quid-%app%-%id%
		END:VEVENT
		END:VCALENDAR'
    ];


    // event
    // retourne la string calendar pour un tableau événement
    public function event(array $array):?string
    {
        $return = null;
        $array = Base\Obj::cast($array);

        if(Base\Arr::keysExists(['dateStart','dateEnd','name','description','location','uri','id'],$array))
        {
            if(is_int($array['id']) && is_int($array['dateStart']) && is_int($array['dateEnd']) && is_string($array['name']) && is_string($array['uri']))
            {
                $app = (!empty($array['app']))? str_replace('-','_',Base\Slug::str($array['app'])):null;
                $model = Base\Str::removeTabs(static::$config['model']);
                $timezone = Base\Timezone::get();
                $name = Base\Str::excerpt(255,str_replace(',',"\,",$array['name']),['removeLineBreaks'=>true]);
                $location = Base\Str::excerpt(255,str_replace(',',"\,",$array['location'] ?? ''),['removeLineBreaks'=>true]);
                $description = Base\Str::excerpt(255,str_replace(',',"\,",$array['description'] ?? ''),['removeLineBreaks'=>true]);
                $replace = [];

                $replace['%timezone%'] = $timezone;
                $replace['%dateStart%'] = Base\Date::format('ics',$array['dateStart']);
                $replace['%dateEnd%'] = Base\Date::format('ics',$array['dateEnd']);
                $replace['%name%'] = $name;
                $replace['%location%'] = $location;
                $replace['%description%'] = $description;
                $replace['%uri%'] = $array['uri'];
                $replace['%app%'] = $app;
                $replace['%id%'] = $array['id'];

                $return = Base\Str::replace($replace,$model);
            }
        }

        return $return;
    }


    // writeEvent
    // écrit la string événement dans le fichier calendar
    public function writeEvent(array $array):self
    {
        $event = $this->event($array);

        if(is_string($event))
        $this->write($event);

        return $this;
    }
}

// init
Calendar::__init();
?>