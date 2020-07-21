<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main\Contract;

// cache
// interface to detail the methods required for implementing caching functionality to an object
interface Cache
{
    // getData
    // retourne le tableau des donnés de la cache
    public function getData():array;


    // getContent
    // retourne les donnés de la cache sous forme de string
    public function getContent():string;


    // getDate
    // retourne la date de création de la cache
    public function getDate():int;


    // store
    // enregistre une nouvelle entrée de cache
    public static function store(array $context,string $data):?int;


    // clearAll
    // vide la cache de toutes ses entrées
    public static function clearAll():void;


    // findByContext
    // retourne la cache par contexte
    public static function findByContext(array $context):?self;
}
?>