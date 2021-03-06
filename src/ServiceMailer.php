<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main;
use Quid\Base;

// serviceMailer
// abstract class with basic methods that needs to be extended by a mailing service
abstract class ServiceMailer extends Service
{
    // config
    protected static array $config = [
        'queue'=>null, // queue pour email
        'log'=>null, // classe pour log
        'key'=>null, // clé pour le service
        'name'=>null, // nom from par défaut
        'email'=>null, // email form par défaut
        'username'=>null, // username pour connexion smtp
        'password'=>null // password pour connection smtp
    ];


    // dispatch
    protected static string $dispatch = 'queue'; // détermine la méthode à utiliser pour dispatch


    // dynamique
    protected ?object $mailer = null; // contient l'instance du mailer


    // construct
    // construit l'objet mail
    final public function __construct(?array $attr=null)
    {
        parent::__construct($attr);
        $this->prepare();
    }


    // prepare
    // prépare l'objet et créer l'instance de l'objet mailer
    abstract protected function prepare():void;


    // error
    // retourne la dernière erreur sur l'objet mailer
    abstract public function error():string;


    // isActive
    // retourne vrai si l'envoie de courriel est activé globalement
    final public function isActive():bool
    {
        return Base\Email::isActive();
    }


    // username
    // retourne le username des paramètres de connection
    // sera utilisé comme email s'il n'y a pas de from spécifié
    final public function username():string
    {
        return $this->getAttr('username');
    }


    // from
    // retourne le from par défaut à utiliser
    // utilise la clé email ou username pour déterminer le email par défaut
    // utilise la clé name pour déterminer le nom par défaut
    // le tableau message a priorité sur cette valeur
    final public function from():?array
    {
        $return = null;
        $email = $this->getAttr('email');

        if(!Base\Validate::isEmail($email))
        $email = $this->username();

        if(Base\Validate::isEmail($email))
        {
            $name = $this->getAttr('name');
            $return = ['email'=>$email];

            if(is_string($name))
            $return['name'] = $name;
        }

        return $return;
    }


    // reset
    // délie l'objet mailer, l'objet n'est plus isReady
    final public function reset():void
    {
        $this->checkReady();
        $this->mailer = null;
    }


    // mailer
    // retourne l'instance de l'objet mailer
    final public function mailer():object
    {
        return $this->checkReady()->mailer;
    }


    // isReady
    // retourne vrai si l'objet email est prêt à être utilisé
    final public function isReady():bool
    {
        return !empty($this->mailer);
    }


    // checkReady
    // lance une exception si le status n'est pas le même que celui donné en argument
    final public function checkReady(bool $value=true)
    {
        $ready = $this->isReady();

        if($value === true && $ready === false)
        static::throw('mailerNotInstantiated');

        elseif($value === false && $ready === true)
        static::throw('mailerInstantiated');

        return $this;
    }


    // prepareMessage
    // peut soumettre un objet avec la méthode sendEmail
    // met le username comme email par défaut si from n'est pas spécifié
    // envoie une exception si le message est invalide après préparation
    // sinon retourne le tableau message
    final public function prepareMessage($value):array
    {
        $return = null;
        $value = $this->messageCastObj($value);

        if(is_array($value))
        {
            foreach ($value as $k => $v)
            {
                if(is_object($v) && $v->hasMethod('toEmail'))
                $value[$k] = $v->toEmail();
            }

            if(empty($value['from']))
            $value['from'] = $this->from();

            $value = Base\Arr::cleanNull($value);
            $return = Base\Email::prepareMessage($value,false);
        }

        return $return ?: static::throw('invalidMessage');
    }


    // messageCastObj
    // cast la valuer si c'est un objet avec une méthode sendEmail
    // utilisé pour modèles de courriel
    final protected function messageCastObj($return)
    {
        if(is_object($return) && $return->hasMethod('sendEmail'))
        $return = $return->sendEmail();

        return $return;
    }


    // messageWithOption
    // retourne un tableau mergé message et options, sans le mot de passe
    // inclut error s'il y a
    // utilisé par les méthodes log
    final public function messageWithOption(array $value):array
    {
        $return = Base\Arr::replace($this->attr(),$value);
        $return['key'] = $this->getServiceKey(true);

        $error = $this->error();
        if(!empty($error))
        $return['error'] = $error;

        $strip = ['password'];
        return Base\Arr::keysStrip($strip,$return);
    }


    // send
    // envoie le courriel maintenant
    final public function send($value):bool
    {
        return $this->trigger($value);
    }


    // sendOnCloseDown
    // envoie le courriel au closeDown de l'application
    // retourne null
    final public function sendOnCloseDown($value):void
    {
        Base\Response::onCloseDown(fn() => $this->trigger($value));
    }


    // sendNowOrOnCloseDown
    // permet d'envoyer un courriel à partir d'un tableau message
    // tous les champs contact, sauf from, peuvent avoir de multiples destinataires
    // peut envoyer maintenant ou au closeDown
    final protected function sendNowOrOnCloseDown($value,bool $onCloseDown=false):?bool
    {
        return ($onCloseDown === true)? $this->sendOnCloseDown($value):$this->send($value);
    }


    // sendTest
    // permet d'envoyer un courriel test
    final public function sendTest($value=null,bool $onCloseDown=false):?bool
    {
        $value = $this->messageCastObj($value);

        if($value !== null && !is_array($value))
        static::throw();

        $value = Base\Email::prepareTestMessage($value);
        return $this->sendNowOrOnCloseDown($value,$onCloseDown);
    }


    // sendLoop
    // permet d'envoyer plusieurs messages à partir d'un tableau multidimensionnel
    final public function sendLoop(array $values,bool $onCloseDown=false):array
    {
        return Base\Arr::accumulate([],$values,fn($value) => $this->sendNowOrOnCloseDown($value,$onCloseDown));
    }


    // queue
    // permettre de mettre un message dans la table de queue
    // envoie une exception si la classe n'existe pas
    // la queue peut être vidé dans le script, au closeDown, ou via un script cron
    // si queue failed, c'est probablement à cause des permissions de l'user
    final public function queue($value):bool
    {
        $message = $this->prepareMessage($value);
        $message = $this->messageWithOption($message,false);
        $queue = $this->queueClass() ?: static::throw('noQueueClass');
        $row = $queue::queue($message) ?: static::throw('emailQueueFailed');

        return true;
    }


    // queueTest
    // permet de queue un courriel test
    final public function queueTest($value):bool
    {
        $value = $this->messageCastObj($value);

        if($value !== null && !is_array($value))
        static::throw();

        $value = Base\Email::prepareTestMessage($value);
        return $this->queue($value);
    }


    // queueLoop
    // permet de queue plusieurs messages à partir d'un tableau multidimensionnel
    final public function queueLoop(array $values):array
    {
        return Base\Arr::accumulate([],$values,fn($value) => $this->queue($value));
    }


    // dispatch
    // permet de dispatch un message
    // la méthode utilisée est déterminée dans la configuration de la classe
    // envoie une exception si la méthode est invalide
    final public function dispatch($value):?bool
    {
        $dispatch = static::getDispatch();
        return $this->$dispatch($value);
    }


    // dispatchLoop
    // permet de dispatch plusieurs messages à partir d'un tableau multidimensionnel
    final public function dispatchLoop(array $values):array
    {
        return Base\Arr::accumulate([],$values,fn($value) => $this->dispatch($value));
    }


    // log
    // permet de log un message si une classe log est lié
    final protected function log(bool $status,array $message):void
    {
        $log = $this->logClass();

        if(!empty($log))
        $log::log($status,$this->messageWithOption($message,false));
    }


    // queueClass
    // retourne la classe pour queue
    // envoie une classe si la classe est invalide
    final public function queueClass():?string
    {
        $return = $this->getAttr('queue');

        if(is_string($return) && !is_a($return,Contract\Queue::class,true))
        static::throw('invalidQueueClass',$return);

        return $return;
    }


    // logClass
    // retourne la classe pour log
    // envoie une classe si la classe est invalide
    final public function logClass():?string
    {
        $return = $this->getAttr('log');

        if(is_string($return) && !is_a($return,Contract\Log::class,true))
        static::throw('invalidLogClass',$return);

        return $return;
    }


    // getDispatch
    // retourne la méthode dispatch
    final public static function getDispatch():string
    {
        return static::$dispatch;
    }


    // setDispatch
    // change la méthode de dispatch
    final public static function setDispatch(string $value):void
    {
        if(in_array($value,['send','sendOnCloseDown','queue'],true))
        static::$dispatch = $value;

        else
        static::throw($value);
    }


    // getOverloadKeyPrepend
    // retourne le prepend de la clé à utiliser pour le tableau overload
    final public static function getOverloadKeyPrepend():?string
    {
        return (static::class !== self::class && !Base\Fqcn::sameName(static::class,self::class))? 'Service':null;
    }
}

// init
ServiceMailer::__init();
?>