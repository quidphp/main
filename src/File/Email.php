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

// email
// class for a file which is an email (in json format)
class Email extends Json implements Main\Contract\Email
{
    // trait
    use Main\_email;


    // config
    public static $config = [
        'mailer'=>[]
    ];


    // contentType
    // retourne le type de contenu du email, retourne string
    final public function contentType():string
    {
        return $this->readGet('contentType');
    }


    // subject
    // retourne le sujet du email
    final public function subject():string
    {
        return $this->readGet('subject');
    }


    // body
    // retourne le body du email
    final public function body():string
    {
        return $this->readGet('body');
    }


    // serviceMailer
    // retourne l'objet mailer à utiliser pour envoyer le courriel
    // peut envoyer une exception
    final public static function serviceMailer($key=null):Main\ServiceMailer
    {
        $return = null;

        if(is_string($key) && array_key_exists($key,static::$config['mailer']))
        $return = static::$config['mailer'][$key];
        else
        $return = current(static::$config['mailer']);

        if(empty($return))
        static::throw('noMailerAtKey',$key);

        return $return;
    }


    // setMailer
    // permet de lier un objet mailer à la classe
    // key doit être une string
    final public static function setMailer(string $key,Main\ServiceMailer $value):void
    {
        static::$config['mailer'][$key] = $value;

        return;
    }
}

// init
Email::__init();
?>