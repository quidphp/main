<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main\Contract;

// session
// interface to detail the methods required for an objet to be a session storage
interface Session
{
    // sessionSid
    // retourne la clé de session
    public function sessionSid():string;


    // sessionData
    // retourne les données de la session
    public function sessionData():string;


    // sessionWrite
    // écrit de nouvelles data dans la session
    public function sessionWrite(string $data):bool;


    // sessionUpdateTimestamp
    // update le timestamp de la session, retourne true même si rien n'a changé
    public function sessionUpdateTimestamp():bool;


    // sessionDestroy
    // détruit la session
    public function sessionDestroy():bool;


    // sessionExists
    // retourne vrai si le sid exists pour le nom donné
    public static function sessionExists(string $path,string $name,string $sid):bool;


    // sessionCreate
    // crée une nouvelle session avec le nom et side donné
    public static function sessionCreate(string $path,string $name,string $sid):?self;


    // sessionRead
    // lit une session à partir d'un nom et d'un sid
    // retourne une classe qui implémente Contract\Session
    public static function sessionRead(string $path,string $name,string $sid):?self;


    // sessionGarbageCollect
    // lance le processus de garbageCollect pour le nom de session donné
    public static function sessionGarbageCollect(string $path,string $name,int $lifetime,$not=null):int;
}
?>