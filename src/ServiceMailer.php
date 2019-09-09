<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main;
use Quid\Base;

// serviceMailer
// abstract class with basic methods that needs to be extended by a mailing service
abstract class ServiceMailer extends Service
{
    // config
    public static $config = [
        'queue'=>null, // queue pour email
        'log'=>null, // classe pour log
        'option'=>[
            'key'=>null, // clé pour le service
            'name'=>null, // nom from par défaut
            'email'=>null, // email form par défaut
            'username'=>null, // username pour connexion smtp
            'password'=>null] // password pour connection smtp
    ];


    // dispatch
    protected static $dispatch = 'queue'; // détermine la méthode à utiliser pour dispatch


    // dynamique
    protected $mailer = null; // contient l'instance du mailer


    // construct
    // construit l'objet mail
    public function __construct(string $key,?array $option=null)
    {
        parent::__construct($key,$option);
        $this->prepare();

        return;
    }


    // prepare
    // prépare l'objet et créer l'instance de l'objet mailer
    abstract protected function prepare():self;


    // error
    // retourne la dernière erreur sur l'objet mailer
    abstract public function error():string;


    // isActive
    // retourne vrai si l'envoie de courriel est activé globalement
    public function isActive():bool
    {
        return Base\Email::isActive();
    }


    // username
    // retourne le username des paramètres de connection
    // sera utilisé comme email s'il n'y a pas de from spécifié
    public function username():string
    {
        return $this->getOption('username');
    }


    // from
    // retourne le from par défaut à utiliser
    // utilise la clé email ou username pour déterminer le email par défaut
    // utilise la clé name pour déterminer le nom par défaut
    // le tableau message a priorité sur cette valeur
    public function from():?array
    {
        $return = null;
        $email = $this->getOption('email');

        if(!Base\Validate::isEmail($email))
        $email = $this->username();

        if(Base\Validate::isEmail($email))
        {
            $name = $this->getOption('name');
            $return = ['email'=>$email];

            if(is_string($name))
            $return['name'] = $name;
        }

        return $return;
    }


    // reset
    // délie l'objet mailer, l'objet n'est plus isReady
    public function reset():self
    {
        $this->checkReady();
        $this->mailer = null;

        return $this;
    }


    // mailer
    // retourne l'instance de l'objet mailer
    public function mailer():object
    {
        return $this->checkReady()->mailer;
    }


    // isReady
    // retourne vrai si l'objet email est prêt à être utilisé
    public function isReady():bool
    {
        return (!empty($this->mailer))? true:false;
    }


    // checkReady
    // lance une exception si le status n'est pas le même que celui donné en argument
    public function checkReady(bool $value=true)
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
    public function prepareMessage($value):array
    {
        $return = null;
        $value = $this->messageCastObj($value);

        if(is_array($value))
        {
            foreach ($value as $k => $v)
            {
                if(is_object($v) && method_exists($v,'toEmail'))
                $value[$k] = $v->toEmail();
            }

            if(empty($value['from']))
            $value['from'] = $this->from();

            $value = Base\Arr::cleanNull($value);
            $return = Base\Email::prepareMessage($value,false);
        }

        if(empty($return))
        static::throw('invalidMessage');

        return $return;
    }


    // messageCastObj
    // cast la valuer si c'est un objet avec une méthode sendEmail
    // utilisé pour modèles de courriel
    // méthode protégé
    protected function messageCastObj($return)
    {
        if(is_object($return) && method_exists($return,'sendEmail'))
        $return = $return->sendEmail();

        return $return;
    }


    // messageWithOption
    // retourne un tableau mergé message et options, sans le mot de passe
    // inclut error s'il y a
    // utilisé par les méthodes log
    public function messageWithOption(array $value):array
    {
        $return = Base\Arr::replace($this->option(),$value);
        $return['key'] = $this->getKey();

        $error = $this->error();
        if(!empty($error))
        $return['error'] = $error;

        $strip = ['password'];
        $return = Base\Arr::keysStrip($strip,$return);

        return $return;
    }


    // send
    // envoie le courriel maintenant
    public function send($value):bool
    {
        return $this->trigger($value);
    }


    // sendOnCloseDown
    // envoie le courriel au closeDown de l'application
    // retourne null
    public function sendOnCloseDown($value):void
    {
        Base\Response::onCloseDown(function() use($value) {
            $this->trigger($value);
        });

        return;
    }


    // sendNowOrOnCloseDown
    // permet d'envoyer un courriel à partir d'un tableau message
    // tous les champs contact, sauf from, peuvent avoir de multiples destinataires
    // peut envoyer maintenant ou au closeDown
    // méthode protégé
    protected function sendNowOrOnCloseDown($value,bool $onCloseDown=false):?bool
    {
        return ($onCloseDown === true)? $this->sendOnCloseDown($value):$this->send($value);
    }


    // sendTest
    // permet d'envoyer un courriel test
    public function sendTest($value=null,bool $onCloseDown=false):?bool
    {
        $return = null;
        $value = $this->messageCastObj($value);

        if($value === null || is_array($value))
        {
            $value = Base\Email::prepareTestMessage($value);
            $return = $this->sendNowOrOnCloseDown($value,$onCloseDown);
        }

        else
        static::throw();

        return $return;
    }


    // sendLoop
    // permet d'envoyer plusieurs messages à partir d'un tableau multidimensionnel
    public function sendLoop(array $values,bool $onCloseDown=false):array
    {
        $return = [];

        foreach ($values as $key => $value)
        {
            $return[$key] = $this->sendNowOrOnCloseDown($value,$onCloseDown);
        }

        return $return;
    }


    // queue
    // permettre de mettre un message dans la table de queue
    // envoie une exception si la classe n'existe pas
    // la queue peut être vidé dans le script, au closeDown, ou via un script cron
    // si queue failed, c'est probablement à cause des permissions de l'user
    public function queue($value):bool
    {
        $return = false;
        $message = $this->prepareMessage($value);
        $queue = static::queueClass();

        if(!empty($queue))
        {
            $row = $queue::queue($this->messageWithOption($message,false));

            if(!empty($row))
            $return = true;

            else
            static::throw('emailQueueFailed');
        }

        else
        static::throw('noQueueClass');

        return $return;
    }


    // queueTest
    // permet de queue un courriel test
    public function queueTest($value):bool
    {
        $return = null;
        $value = $this->messageCastObj($value);

        if($value === null || is_array($value))
        {
            $value = Base\Email::prepareTestMessage($value);
            $return = $this->queue($value);
        }

        else
        static::throw();

        return $return;
    }


    // queueLoop
    // permet de queue plusieurs messages à partir d'un tableau multidimensionnel
    public function queueLoop(array $values):array
    {
        $return = [];

        foreach ($values as $key => $value)
        {
            $return[$key] = $this->queue($value);
        }

        return $return;
    }


    // dispatch
    // permet de dispatch un message
    // la méthode utilisée est déterminée dans la configuration de la classe
    // envoie une exception si la méthode est invalide
    public function dispatch($value):?bool
    {
        $return = null;
        $dispatch = static::getDispatch();
        $return = $this->$dispatch($value);

        return $return;
    }


    // dispatchLoop
    // permet de dispatch plusieurs messages à partir d'un tableau multidimensionnel
    public function dispatchLoop(array $values):array
    {
        $return = [];

        foreach ($values as $key => $value)
        {
            $return[$key] = $this->dispatch($value);
        }

        return $return;
    }


    // log
    // permet de log un message si une classe log est lié
    // méthode protégé
    protected function log(bool $status,array $message):self
    {
        $log = static::logClass();
        if(!empty($log))
        $log::new($status,$this->messageWithOption($message,false));

        return $this;
    }


    // queueClass
    // retourne la classe pour queue
    // envoie une classe si la classe est invalide
    public static function queueClass():?string
    {
        $return = static::$config['queue'];

        if(is_string($return) && !is_a($return,Contract\Queue::class,true))
        static::throw('invalidQueueClass',$return);

        return $return;
    }


    // logClass
    // retourne la classe pour log
    // envoie une classe si la classe est invalide
    public static function logClass():?string
    {
        $return = static::$config['log'];

        if(is_string($return) && !is_a($return,Contract\Log::class,true))
        static::throw('invalidLogClass',$return);

        return $return;
    }


    // getDispatch
    // retourne la méthode dispatch
    public static function getDispatch():string
    {
        return static::$dispatch;
    }


    // setDispatch
    // change la méthode de dispatch
    public static function setDispatch(string $value):void
    {
        if(in_array($value,['send','sendOnCloseDown','queue'],true))
        static::$dispatch = $value;

        else
        static::throw($value);

        return;
    }
}
?>