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

// queue
// class for a queue storage file
class Queue extends Serialize implements Main\Contract\Queue, Main\Contract\FileStorage
{
    // trait
    use _storage;
    use Main\_queue;


    // config
    public static $config = [
        'dirname'=>'[storage]/queue',
        'extension'=>'txt',
        'unqueue'=>null // callable à mettre pour le unqueue
    ];


    // onUnqueue
    // sur unqueue efface le fichier automatiquement
    protected function onUnqueue():void
    {
        $this->unlink();

        return;
    }


    // unqueue
    // permet de faire unqueue du fichier
    // envoie une exception si pas de callable lié
    public function unqueue()
    {
        $return = null;
        $callable = $this->getAttr('unqueue');

        if(static::classIsCallable($callable))
        $return = Base\Call::withObj($this,$callable);

        else
        static::throw('noCallableForUnqueue');

        return $return;
    }


    // queue
    // créer une nouvelle entrée dans la queue
    // incremente la valeur inc
    public static function queue(...$values):?Main\Contract\Queue
    {
        return static::storage(...$values);
    }


    // getQueued
    // retourne un objet avec toutes les entrées queued
    // la plus ancienne est retourné en premier
    public static function getQueued(?int $limit=null):?Main\Map
    {
        return static::storageSort(false,$limit);
    }


    // setUnqueueCallable
    // permet d'attribuer une callable pour le unqueue
    public static function setUnqueueCallable(callable $callable):void
    {
        static::$config['unqueue'] = $callable;

        return;
    }
}

// init
Queue::__init();
?>