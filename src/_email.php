<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main;
use Quid\Base;

// _email
// trait that provides methods to use the object as an email
trait _email
{
    // config
    public static $configEmail = [
        'segment'=>null // custom, caractère à utiliser pour les segments
    ];


    // serviceMailer
    // retourne l'objet mailer à utiliser pour envoyer le courriel
    abstract public static function serviceMailer($key):ServiceMailer;


    // messageSegment
    // retourne un tableau avec les segments requis pour envoyer le email
    public function messageSegment():array
    {
        $return = [];
        $delimiter = $this->getSegmentChars();
        $subject = $this->subject();
        $body = $this->body();
        $return = Base\Arr::appendUnique($return,Base\Segment::get($delimiter,$subject),Base\Segment::get($delimiter,$body));

        return $return;
    }


    // message
    // prépare le tableau message à envoyer dans core/email
    // seul to est requis et peut prendre plusieurs formes (string, array)
    // une exception est envoyé si tous les segments requis ne sont pas fournis dans replace
    public function prepareMessage($to,?array $replace=null,?array $return=null):array
    {
        $return = ($return === null)? []:$return;
        $replace = ($replace === null)? []:$replace;
        $replace = Base\Obj::cast($replace);
        $segment = $this->messageSegment();

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
            $return['subject'] = Base\Segment::sets($delimiter,$replace,$this->subject());
            $return['body'] = Base\Segment::sets($delimiter,$replace,$this->body());
        }

        return $return;
    }


    // sendMethod
    // méthode protégé pour envoyer ou queue le email
    // la méthode peut retourner null dans le cas de sendOnCloseDown
    protected function sendMethod(string $method,$key,$to,?array $replace=null,?array $message=null):?bool
    {
        $return = false;
        $email = ($key instanceof ServiceMailer)? $key:static::serviceMailer($key);
        $message = $this->prepareMessage($to,$replace,$message);

        if(!empty($email))
        {
            if(!empty($message))
            $return = $email->$method($message);

            else
            static::throw('invalidMesasge');
        }

        else
        static::throw('noEmailObject');

        return $return;
    }


    // send
    // envoie le courriel maintenant
    public function send($key,$to,?array $replace=null,?array $message=null):bool
    {
        return $this->sendMethod('send',$key,$to,$replace,$message);
    }


    // sendOnCloseDown
    // envoie le courriel à la fermeture du script ou de boot
    public function sendOnCloseDown($key,$to,?array $replace=null,?array $message=null):void
    {
        $this->sendMethod('sendOnCloseDown',$key,$to,$replace,$message);

        return;
    }


    // queue
    // queue le courriel pour envoie plus tard
    public function queue($key,$to,?array $replace=null,?array $message=null):bool
    {
        return $this->sendMethod('queue',$key,$to,$replace,$message);
    }


    // dispatch
    // dispatch le courriel selon la méthode par défaut défini dans la classe de courriel
    public function dispatch($key,$to,?array $replace=null,?array $message=null):bool
    {
        return $this->sendMethod('dispatch',$key,$to,$replace,$message);
    }


    // getSegmentChars
    // retourne les caractères de segments à utiliser
    public function getSegmentChars()
    {
        return $this->getAttr('segment');
    }
}
?>