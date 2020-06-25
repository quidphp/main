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
use Quid\Main;

// queue
// class for a queue storage file
class Queue extends Serialize implements Main\Contract\Queue, Main\Contract\FileStorage
{
    // trait
    use _storage;
    use Main\_queue;


    // config
    protected static array $config = [
        'dirname'=>'[storage]/queue',
        'extension'=>'txt',
        'unqueue'=>null // callable à mettre pour le unqueue
    ];


    // onUnqueue
    // sur unqueue efface le fichier automatiquement
    final protected function onUnqueue():void
    {
        $this->unlink();
    }


    // unqueue
    // permet de faire unqueue du fichier
    // envoie une exception si pas de callable lié
    final public function unqueue()
    {
        $return = null;
        $callable = $this->getAttr('unqueue');

        if(static::isCallable($callable))
        $return = $callable($this);

        else
        static::throw('noCallableForUnqueue');

        return $return;
    }


    // queue
    // créer une nouvelle entrée dans la queue
    // incremente la valeur inc
    final public static function queue(...$values):?Main\Contract\Queue
    {
        return static::storage(...$values);
    }


    // getQueued
    // retourne un objet avec toutes les entrées queued
    // la plus ancienne est retourné en premier
    final public static function getQueued(?int $limit=null):?Main\Map
    {
        return static::storageSort(false,$limit);
    }


    // setUnqueueCallable
    // permet d'attribuer une callable pour le unqueue
    final public static function setUnqueueCallable(\Closure $closure):void
    {
        static::$config['unqueue'] = $closure;
    }
}

// init
Queue::__init();
?>