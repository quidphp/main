<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main;
use Quid\Base;

// _email
// trait that provides methods to use the object as an email
trait _email
{
    // config
    protected static array $configEmail = [
        'segment'=>null // custom, caractère à utiliser pour les segments
    ];


    // serviceMailer
    // retourne l'objet mailer à utiliser pour envoyer le courriel
    abstract public static function serviceMailer($key):ServiceMailer;


    // messageSegment
    // retourne un tableau avec les segments requis pour envoyer le email
    final public function messageSegment(?string $lang=null):array
    {
        $return = [];
        $delimiter = $this->getSegmentChars();
        $subject = $this->subject($lang);
        $body = $this->body($lang);
        $return = Base\Arr::mergeUnique($return,Base\Segment::get($delimiter,$subject),Base\Segment::get($delimiter,$body));

        return $return;
    }


    // message
    // prépare le tableau message à envoyer dans core/email
    // seul to est requis et peut prendre plusieurs formes (string, array)
    // une exception est envoyé si tous les segments requis ne sont pas fournis dans replace
    final public function prepareMessage($to,?array $replace=null,?string $lang=null,?array $return=null):array
    {
        $return ??= [];
        $replace ??= [];
        $replace = Base\Obj::cast($replace);
        $segment = $this->messageSegment($lang);

        if(empty($to))
        static::throw('to','required');

        elseif(!empty($segment) && !Base\Arr::keysExists($segment,$replace))
        {
            $missing = Base\Arr::valuesStrip(array_keys($replace),$segment);
            static::throw('missingSegment',...$missing);
        }

        else
        {
            $delimiter = $this->getSegmentChars();
            $return['to'] = $to;
            $return['contentType'] = $this->contentType();
            $return['subject'] = Base\Segment::sets($delimiter,$replace,$this->subject($lang));
            $return['body'] = Base\Segment::sets($delimiter,$replace,$this->body($lang));
        }

        return $return;
    }


    // sendMethod
    // méthode protégé pour envoyer ou queue le email
    // la méthode peut retourner null dans le cas de sendOnCloseDown
    final protected function sendMethod(string $method,$key,$to,?array $replace=null,?string $lang=null,?array $message=null):?bool
    {
        $email = ($key instanceof ServiceMailer)? $key:static::serviceMailer($key);
        $message = $this->prepareMessage($to,$replace,$lang,$message) ?: static::throw('invalidMesasge');

        if(empty($email))
        static::throw('noEmailObject');

        return $email->$method($message);
    }


    // send
    // envoie le courriel maintenant
    final public function send($key,$to,?array $replace=null,?string $lang=null,?array $message=null):bool
    {
        return $this->sendMethod('send',$key,$to,$replace,$lang,$message);
    }


    // sendOnCloseDown
    // envoie le courriel à la fermeture du script ou de boot
    final public function sendOnCloseDown($key,$to,?array $replace=null,?string $lang=null,?array $message=null):void
    {
        $this->sendMethod('sendOnCloseDown',$key,$to,$replace,$lang,$message);
    }


    // queue
    // queue le courriel pour envoie plus tard
    final public function queue($key,$to,?array $replace=null,?string $lang=null,?array $message=null):bool
    {
        return $this->sendMethod('queue',$key,$to,$replace,$lang,$message);
    }


    // dispatch
    // dispatch le courriel selon la méthode par défaut défini dans la classe de courriel
    final public function dispatch($key,$to,?array $replace=null,?string $lang=null,?array $message=null):bool
    {
        return $this->sendMethod('dispatch',$key,$to,$replace,$lang,$message);
    }


    // getSegmentChars
    // retourne les caractères de segments à utiliser
    final public function getSegmentChars()
    {
        return $this->getAttr('segment');
    }
}
?>