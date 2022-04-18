<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main;
use Quid\Base;

// session
// class that implements the methods necessary for the SessionHandlerInterface interface
class Session extends Map implements \SessionHandlerInterface, \SessionUpdateTimestampHandlerInterface
{
    // trait
    use Map\_arrs;
    use _inst;


    // config
    protected static array $config = [
        'base'=>[ // toutes les méthodes renvoyé à base, la session doit être ready
            'isLang','isIp','isCsrf','isCaptcha','isBot',
            'getPrefix','expire','timestampCurrent','timestampPrevious','timestampDifference','requestCount','resetRequestCount',
            'userAgent','env','type','ip','fingerprint',
            'lang','csrf','refreshCsrf','getCsrfName','captcha','refreshCaptcha','emptyCaptcha','getCaptchaName','version',
            'remember','setRemember','setsRemember','unsetRemember','rememberEmpty'],
        'historyClass'=>RequestHistory::class, // classe pour historique de requête
        'env'=>null, // défini l'environnement de la session
        'type'=>null, // défini le type de la session
        'version'=>null, // défini la version de la session
        'structure'=>[ // callables de structure additionnelles dans data, se merge à celle dans base/session
            'flash'=>'structureFlash',
            'history'=>'structureHistory',
            'timeout'=>'structureTimeout',
            'com'=>'structureCom'],
        'setCookie'=>true, // le cookie est réenvoyé à chaque démarrage de la session
        'registerShutdown'=>true // le setSaveHandler créer la shutdown function pour session_write_close
    ];


    // dynamique
    protected string $class; // set la classe du storage
    protected ?Contract\Session $storage = null; // objet de storage de la session
    protected ?array $mapAllow = ['set','unset','remove','sort','empty']; // méthodes permises


    // construct
    // construit l'objet session et merge les attrs
    // démarre la session
    final public function __construct(string $class,?array $attr=null)
    {
        $this->makeAttr($attr);
        $this->setStorageClass($class);
        $this->start();
    }


    // onCheckArr
    // callback avant accès à arr
    final protected function onCheckArr():void
    {
        $this->checkReady();
    }


    // onStart
    // callback une fois que la session a été démarré
    protected function onStart():void
    {
        $this->data =& $_SESSION;

        $com = $this->com();
        $class = Error::classOverload();
        $class::setCom($com);
    }


    // onEnd
    // callback une fois que la session a terminé
    protected function onEnd():void
    {
        $this->storage = null;
        $class = Error::classOverload();
        $class::setCom(null);
    }


    // onPrepareUnsetInst
    // méthode appeler avant unsetInst
    final protected function onPrepareUnsetInst():void
    {
        if($this->isReady())
        $this->commit();
    }


    // call
    // renvoie des méthodes à base
    // la session doit être active pour les méthodes dans config/base
    final public function __call(string $key,array $args)
    {
        $return = null;
        $base = $this->getAttr('base');

        if(is_array($base) && in_array($key,$base,true))
        {
            $this->checkReady();
            $return = Base\Session::$key(...$args);
        }

        elseif(Base\Session::classHasMethod($key))
        $return = Base\Session::$key(...$args);

        else
        static::throw($key,'methodDoesNotExist');

        return $return;
    }


    // setStorageClass
    // applique la classe de storage
    final protected function setStorageClass(string $value):void
    {
        if(!class_exists($value,true) || !$value::classHasInterface(Contract\Session::class))
        static::throw();

        $this->class = $value;
    }


    // getStorageClass
    // retourne le classe de storage
    final public function getStorageClass():string
    {
        return $this->class;
    }


    // isStarted
    // retourne vrai si la session est démarré et lié
    final public function isStarted():bool
    {
        return session_status() === PHP_SESSION_ACTIVE;
    }


    // isReady
    // retourne vrai si la session est démarré et lié à inst
    final public function isReady():bool
    {
        return $this->isStarted() && $this->hasStorage();
    }


    // checkStarted
    // retourne vrai si la session est démarré, envoie une exception si non
    final protected function checkStarted():self
    {
        if($this->isStarted() === false)
        static::throw();

        return $this;
    }


    // checkReady
    // retourne vrai si la session est ready, envoie une exception si non
    final protected function checkReady():self
    {
        if($this->isReady() === false)
        static::throw();

        return $this;
    }


    // getStructure
    // retourne la structure de la session à partir des attrs
    // si une valeur de structure est seulement une string, c'est une méthode de cette classe
    // merge avec la structure de base/session
    final public function getStructure():array
    {
        $structure = $this->getAttr('structure');

        if(is_array($structure))
        {
            foreach ($structure as $key => $value)
            {
                if(is_string($value))
                $structure[$key] = [$this,$value];
            }
        }

        return Base\Session::getStructure($structure);
    }


    // structureFlash
    // gère le champ structure flash de la session
    // mode insert, update ou is
    final public function structureFlash(string $mode,$value=null)
    {
        $return = $value;

        if($mode === 'insert')
        $return = Flash::newOverload();

        elseif($mode === 'is')
        $return = ($value instanceof Flash);

        return $return;
    }


    // structureHistory
    // gère le champ structure history de la session
    // mode insert, update ou is
    final public function structureHistory(string $mode,$value=null)
    {
        $return = $value;
        $class = $this->getAttr('historyClass') ?? RequestHistory::class;

        if($mode === 'insert')
        $return = $class::newOverload();

        elseif($mode === 'is')
        $return = ($value instanceof RequestHistory);

        return $return;
    }


    // structureTimeout
    // gère le champ structure timeout de la session
    // mode insert, update ou is
    final public function structureTimeout(string $mode,$value=null)
    {
        $return = $value;
        $timeout = $this->getAttr('timeout');

        if($mode === 'insert')
        $return = Timeout::newOverload();

        elseif($mode === 'update' && $value instanceof Timeout)
        $value->resetAll();

        elseif($mode === 'is')
        $return = ($value instanceof Timeout);

        return $return;
    }


    // structureCom
    // gère le champ structure com de la session
    // mode insert, update ou is
    final public function structureCom(string $mode,$value=null)
    {
        $return = $value;

        if($mode === 'insert')
        $return = Com::newOverload();

        elseif($mode === 'is')
        $return = ($value instanceof Com);

        return $return;
    }


    // hasStorage
    // retourne vrai si la session a présentement un storage lié
    final public function hasStorage():bool
    {
        return !empty($this->storage);
    }


    // storage
    // retourne la row de la session ou envoie une exception si non existante
    final public function storage():Contract\Session
    {
        return static::typecheck($this->storage,Contract\Session::class);
    }


    // info
    // retourne un tableau contenant un maximum d'information sur la session
    final public function info():array
    {
        $return = ['class'=>static::class];
        $return['storageClass'] = $this->getStorageClass();
        $return['attr'] = $this->attr();
        $return = Base\Arr::merge($return,Base\Session::info());

        return $return;
    }


    // setLang
    // change la langue de la session
    // une exception est envoyé si la langue n'existe pas dans base lang
    public function setLang(string $value):self
    {
        $set = Base\Session::setLang($value);

        if($set === false)
        static::throw($value,'langCodeDoesNotExistIn',Base\Lang::class);

        return $this;
    }


    // checkSid
    // vérifie que le id est valide ou envoie une exception
    final protected function checkSid($sid):self
    {
        $prefix = Base\Session::getPrefix();

        if(!is_string($sid) || !Base\Session::validateId($sid,$prefix))
        static::throw();

        return $this;
    }


    // restart
    // vide, détruit et démarrer la session
    // le id de la session est aussi changé lors du redémarrage
    final public function restart():self
    {
        $this->empty();
        $this->teardown();
        $this->start();

        return $this;
    }


    // regenerateId
    // change le id de session et garde les données
    // possibilité de delete l'ancienne session
    final public function regenerateId(bool $delete=true):self
    {
        $this->checkReady();

        if(!Base\Session::regenerateId($delete))
        static::throw();

        $this->onStart();

        return $this;
    }


    // encode
    // encode le contenu du tableau session selon le serialize handler
    final public function encode():?string
    {
        $this->checkReady();
        return Base\Session::encode();
    }


    // decode
    // decode une string et remplace le contenu du tableau session
    final public function decode(string $value):self
    {
        $this->checkReady();

        if(Base\Session::decode($value) !== true)
        static::throw();

        $this->data =& $_SESSION;

        return $this;
    }


    // reset
    // remplace le contenu du tableau session par les valeurs originales
    final public function reset():self
    {
        $this->checkReady();

        if(Base\Session::reset() !== true)
        static::throw();

        $this->data =& $_SESSION;

        return $this;
    }


    // abort
    // termine la session sans écrire les changements
    // la session doit être active et ne sera pas effacé
    // si unsetArray est true, la superglobale session sera vidé
    final public function abort(bool $unsetArray=true):self
    {
        $this->checkReady();

        if(Base\Session::abort($unsetArray) !== true)
        static::throw();

        return $this;
    }


    // commit
    // écrit les changements et termine la session
    // la session doit être active et ne sera pas effacé
    // si unsetArray est true, le tableau session sera vidé
    final public function commit(bool $unsetArray=true):self
    {
        $this->checkReady();

        if(Base\Session::commit($unsetArray) !== true)
        static::throw();

        return $this;
    }


    // empty
    // efface le contenu de la session et du tableau session
    // la méthode parent de map est appelé
    // la session elle-même n'est pas effacé, seul son contenu est effacé
    final public function empty():self
    {
        $this->checkReady();
        parent::empty();

        if(Base\Session::empty() !== true)
        static::throw();

        return $this;
    }


    // teardown
    // destroy la session
    // possibilité de empty les données et/ou unset le cookie
    final public function teardown(bool $empty=true,bool $unsetCookie=true):self
    {
        $this->checkReady();

        if($empty === true)
        $this->empty();

        if(Base\Session::destroy(false,$unsetCookie) !== true)
        static::throw();

        return $this;
    }


    // garbageCollect
    // lance le processus de garbageCollect et retourne le nombre de sessions effacés
    final public function garbageCollect():?int
    {
        $this->checkReady();
        return Base\Session::garbageCollect();
    }


    // start
    // démarre la session
    // envoie une exception en cas d'échec, lance onStart en cas de succès
    final public function start():self
    {
        if($this->isStarted())
        static::throw('alreadyStarted');

        $this->setDefault();
        $return = Base\Session::start($this->getStructure(),$this->getAttr('setCookie'));

        if($return !== true)
        static::throw('didNotStartProperly');

        $this->onStart();

        return $this;
    }


    // setDefault
    // permet de set les defaults dans base session
    // méthode spécifique pour le sid par défaut, utilisé par cli
    final protected function setDefault():void
    {
        Base\Session::setDefault($this->attr());

        $sid = $this->getSidDefault();
        if(is_string($sid))
        Base\Session::setSid($sid);

        Base\Session::setSaveHandler($this,$this->getAttr('registerShutdown'));
    }


    // getSidDefault
    // retourne le sid à utiliser par défaut
    public function getSidDefault():?string
    {
        return null;
    }


    // history
    // retourne l'objet request history
    final public function history():RequestHistory
    {
        return $this->get('history');
    }


    // historyEmpty
    // vide l'objet request history
    final public function historyEmpty():self
    {
        $this->history()->empty();

        return $this;
    }


    // timeout
    // retourne l'objet timeout
    final public function timeout():Timeout
    {
        return $this->get('timeout');
    }


    // timeoutEmpty
    // vide l'objet timeout
    final public function timeoutEmpty():self
    {
        $timeout = $this->timeout();
        $timeout->empty();

        return $this;
    }


    // com
    // retourne l'objet com
    final public function com():Com
    {
        return $this->get('com');
    }


    // comEmpty
    // vide l'objet com
    final public function comEmpty():self
    {
        $this->com()->empty();

        return $this;
    }


    // flash
    // retourne l'objet session flash
    final public function flash():Flash
    {
        return $this->get('flash');
    }


    // flashEmpty
    // vide l'objet flash
    final public function flashEmpty():self
    {
        $this->flash()->empty();

        return $this;
    }


    // create_sid
    // crée une nouvelle clé de session
    // ne pas appelé directement, remplie une condition de SessionHandlerInterface
    final public function create_sid():?string
    {
        $return = null;
        $this->checkStarted();
        $prefix = Base\Session::getPrefix();
        $return = Base\Session::createSid($prefix);
        $name = Base\Session::name();
        $path = Base\Session::getSavePath(true);
        $class = $this->getStorageClass();

        if($class::sessionExists($path,$name,$return))
        $return = $this->create_sid();

        return $return;
    }


    // open
    // tente d'ouvrir la session
    // ne pas appelé directement, remplie une condition de SessionHandlerInterface
    final public function open($path,$name):bool
    {
        $this->checkStarted();

        if(!is_string($name) || empty($name))
        static::throw('invalidName');

        if($path !== Base\Session::getSavePath())
        static::throw('invalidPath');

        return true;
    }


    // validateId
    // validate le id de session
    // ne pas appelé directement, remplie une condition de SessionHandlerInterface
    final public function validateId($sid):bool
    {
        $this->checkStarted();
        $prefix = Base\Session::getPrefix();

        return Base\Session::validateId($sid,$prefix);
    }


    // read
    // lit le contenu de la session
    // ne pas appelé directement, remplie une condition de SessionHandlerInterface
    final public function read($sid):string
    {
        $this->checkStarted();
        $this->checkSid($sid);
        $name = Base\Session::name();
        $class = $this->getStorageClass();
        $path = Base\Session::getSavePath(true);
        $this->storage = null;
        $storage = $class::sessionRead($path,$name,$sid) ?: $class::sessionCreate($path,$name,$sid);

        if(!$storage instanceof Contract\Session)
        static::throw('couldNotSelectOrInsert');

        $this->storage = $storage;

        return $storage->sessionData();
    }


    // write
    // écrit le contenu dans la session
    // ne pas appelé directement, remplie une condition de SessionHandlerInterface
    final public function write($sid,$data):bool
    {
        $this->checkStarted();
        $this->checkSid($sid);

        return $this->storage()->sessionWrite($data);
    }


    // updateTimestamp
    // met à jour le timestamp de la session, si les données n'ont pas changés
    // ne pas appelé directement, remplie une condition de SessionHandlerInterface
    final public function updateTimestamp($sid,$data):bool
    {
        $this->checkReady();
        $this->checkSid($sid);

        return $this->storage()->sessionUpdateTimestamp();
    }


    // close
    // ferme la session, doit retourner true
    // ne pas appelé directement, remplie une condition de SessionHandlerInterface
    // appele la méthode onEnd
    final public function close():bool
    {
        $this->checkStarted();
        $this->onEnd();

        return true;
    }


    // destroy
    // tente de détruire la session
    // ne pas appelé directement, remplie une condition de SessionHandlerInterface
    final public function destroy($sid):bool
    {
        $this->checkReady();
        $this->checkSid($sid);

        return $this->storage()->sessionDestroy();
    }


    // gc
    // processus de garbage collect
    // ne pas appelé directement, remplie une condition de SessionHandlerInterface
    final public function gc($lifetime):int|false
    {
        $this->checkReady();
        $class = $this->getStorageClass();
        $path = Base\Session::getSavePath(true);
        $name = Base\Session::name();
        $storage = $this->storage;
        $gc = $class::sessionGarbageCollect($path,$name,$lifetime,$storage);

        return is_int($gc) ? $gc:false;
    }
}
?>