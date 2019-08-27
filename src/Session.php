<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/base/blob/master/LICENSE
 */

namespace Quid\Main;
use Quid\Base;

// session
class Session extends Map implements \SessionHandlerInterface, \SessionUpdateTimestampHandlerInterface
{
	// trait
	use Map\_arrs; use _inst; use _option;
	// config
	public static $config = [
		'base'=>[ // toutes les méthodes renvoyé à base
			'isLang','isIp','isCsrf','isCaptcha','isDesktop','isMobile','isOldIe','isMac','isLinux','isWindows','isBot',
			'getPrefix','expire','timestampCurrent','timestampPrevious','timestampDifference','requestCount','resetRequestCount',
			'userAgent','browserCap','browserName','browserPlatform','browserDevice','env','type','ip','fingerprint',
			'lang','csrf','refreshCsrf','captcha','refreshCaptcha','emptyCaptcha','version',
			'remember','setRemember','setsRemember','unsetRemember','emptyRemember'],
		'option'=>[
			'structure'=>[ // callables de structure additionnelles dans data, se merge à celle dans base/session
				'flash'=>'structureFlash',
				'history'=>'structureHistory',
				'timeout'=>'structureTimeout',
				'com'=>'structureCom'],
			'setCookie'=>true, // le cookie est réenvoyé à chaque démarrage de la session
			'registerShutdown'=>true] // le setSaveHandler créer la shutdown function pour session_write_close
	];


	// dynamique
	protected $storage = null; // objet de storage de la session
	protected $class = null; // set la classe du storage


	// map
	protected static $allow = ['set','unset','remove','sort','empty']; // méthodes permises


	// construct
	// construit l'objet session et merge les options
	// démarre la session
	public function __construct(string $class,?array $option=null)
	{
		$this->setStorageClass($class);
		$this->option($option);
		$this->start();

		return;
	}


	// onCheckArr
	// callback avant accès à arr
	protected function onCheckArr():parent
	{
		return $this->checkReady();
	}


	// onStart
	// callback une fois que la session a été démarré
	protected function onStart():self
	{
		$this->data =& $_SESSION;

		$com = $this->com();
		$class = Error::getOverloadClass();
		$class::setCom($com);

		return $this;
	}


	// onEnd
	// callback une fois que la session a terminé
	protected function onEnd():self
	{
		$this->storage = null;
		$class = Error::getOverloadClass();
		$class::setCom(null);

		return $this;
	}


	// onUnsetInst
	// méthode appeler après unsetInst
	// méthode protégé
	protected function onUnsetInst():self
	{
		if($this->isReady())
		$this->commit();

		return $this;
	}


	// call
	// renvoie des méthodes à base
	// la session doit être active pour les méthodes dans config/base
	public function __call(string $key,array $args)
	{
		$return = null;

		if(in_array($key,static::$config['base'],true))
		{
			$this->checkReady();
			$return = Base\Session::$key(...$args);
		}

		elseif(method_exists(Base\Session::class,$key))
		$return = Base\Session::$key(...$args);

		else
		static::throw($key,'methodDoesNotExist');

		return $return;
	}


	// setStorageClass
	// applique la classe de storage
	protected function setStorageClass(string $value):self
	{
		if(class_exists($value,true) && Base\Classe::hasInterface(Contract\Session::class,$value))
		$this->class = $value;

		else
		static::throw();

		return $this;
	}


	// getStorageClass
	// retourne le classe de storage
	public function getStorageClass():string
	{
		return $this->class;
	}


	// isStarted
	// retourne vrai si la session est démarré et lié
	public function isStarted():bool
	{
		return (session_status() === PHP_SESSION_ACTIVE)? true:false;
	}


	// isReady
	// retourne vrai si la session est démarré et lié à inst
	public function isReady():bool
	{
		return ($this->isStarted() && $this->hasStorage())? true:false;
	}


	// checkStarted
	// retourne vrai si la session est démarré, envoie une exception si non
	protected function checkStarted():self
	{
		if($this->isStarted() === false)
		static::throw();

		return $this;
	}


	// checkReady
	// retourne vrai si la session est ready, envoie une exception si non
	protected function checkReady():self
	{
		if($this->isReady() === false)
		static::throw();

		return $this;
	}


	// getStructure
	// retourne la structure de la session à partir des options
	// si une valeur de structure est seulement une string, c'est une méthode de cette classe
	// merge avec la structure de base/session
	public function getStructure():array
	{
		$return = [];
		$structure = $this->getOption('structure');

		if(is_array($structure))
		{
			foreach ($structure as $key => $value)
			{
				if(is_string($value))
				$structure[$key] = [$this,$value];
			}
		}

		$return = Base\Session::getStructure($structure);

		return $return;
	}


	// structureFlash
	// gère le champ structure flash de la session
	// mode insert, update ou is
	public function structureFlash(string $mode,$value=null)
	{
		$return = $value;

		if($mode === 'insert')
		$return = Flash::newOverload();

		elseif($mode === 'is')
		$return = ($value instanceof Flash)? true:false;

		return $return;
	}


	// structureHistory
	// gère le champ structure history de la session
	// mode insert, update ou is
	public function structureHistory(string $mode,$value=null)
	{
		$return = $value;

		if($mode === 'insert')
		$return = RequestHistory::newOverload();

		elseif($mode === 'is')
		$return = ($value instanceof RequestHistory)? true:false;

		return $return;
	}


	// structureTimeout
	// gère le champ structure timeout de la session
	// mode insert, update ou is
	public function structureTimeout(string $mode,$value=null)
	{
		$return = $value;
		$timeout = $this->getOption('timeout');

		if($mode === 'insert')
		$return = Timeout::newOverload();

		elseif($mode === 'update' && $value instanceof Timeout)
		$value->resetAll();

		elseif($mode === 'is')
		$return = ($value instanceof Timeout)? true:false;

		return $return;
	}


	// structureCom
	// gère le champ structure com de la session
	// mode insert, update ou is
	public function structureCom(string $mode,$value=null)
	{
		$return = $value;

		if($mode === 'insert')
		$return = Com::newOverload();

		elseif($mode === 'is')
		$return = ($value instanceof Com)? true:false;

		return $return;
	}


	// hasStorage
	// retourne vrai si la session a présentement un storage lié
	public function hasStorage():bool
	{
		return (!empty($this->storage))? true:false;
	}


	// storage
	// retourne la row de la session ou envoie une exception si non existante
	public function storage():Contract\Session
	{
		$return = $this->storage;

		if(!$return instanceof Contract\Session)
		static::throw();

		return $return;
	}


	// info
	// retourne un tableau contenant un maximum d'information sur la session
	public function info():array
	{
		$return = ['class'=>static::class];
		$return['storageClass'] = $this->getStorageClass();
		$return['option'] = $this->option();
		$return = Base\Arr::append($return,Base\Session::info());

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
	// méthode protégé
	protected function checkSid($sid):self
	{
		$prefix = Base\Session::getPrefix();
		if(!is_string($sid) || !Base\Session::validateId($sid,$prefix))
		static::throw();

		return $this;
	}


	// restart
	// vide, détruit et démarrer la session
	// le id de la session est aussi changé lors du redémarrage
	public function restart():self
	{
		$this->empty();
		$this->terminate();
		$this->start();

		return $this;
	}


	// regenerateId
	// change le id de session et garde les données
	// possibilité de delete l'ancienne session
	public function regenerateId(bool $delete=true):self
	{
		$this->checkReady();

		if(Base\Session::regenerateId($delete))
		$this->onStart();

		else
		static::throw();

		return $this;
	}


	// encode
	// encode le contenu du tableau session selon le serialize handler
	public function encode():?string
	{
		$this->checkReady();
		return Base\Session::encode();
	}


	// decode
	// decode une string et remplace le contenu du tableau session
	public function decode(string $value):self
	{
		$this->checkReady();

		if(Base\Session::decode($value) !== true)
		static::throw();

		$this->data =& $_SESSION;

		return $this;
	}


	// reset
	// remplace le contenu du tableau session par les valeurs originales
	public function reset():self
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
	public function abort(bool $unsetArray=true):self
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
	public function commit(bool $unsetArray=true):self
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
	public function empty():parent
	{
		$this->checkReady();
		parent::empty();

		if(Base\Session::empty() !== true)
		static::throw();

		return $this;
	}


	// terminate
	// destroy la session
	// possibilité de empty les données et/ou unset le cookie
	public function terminate(bool $empty=true,bool $unsetCookie=true):self
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
	public function garbageCollect():?int
	{
		$this->checkReady();
		return Base\Session::garbageCollect();
	}


	// start
	// démarre la session
	// envoie une exception en cas d'échec, lance onStart en cas de succès
	public function start():self
	{
		if(!$this->isStarted())
		{
			Base\Session::setDefault($this->option);
			Base\Session::setSaveHandler($this,$this->getOption('registerShutdown'));
			$return = Base\Session::start($this->getStructure(),$this->getOption('setCookie'));

			if($return === true)
			$this->onStart();

			else
			static::throw('didNotStartProperly');
		}

		else
		static::throw('alreadyStarted');

		return $this;
	}


	// history
	// retourne l'objet request history
	public function history():RequestHistory
	{
		return $this->get('history');
	}


	// historyEmpty
	// vide l'objet request history
	public function historyEmpty():self
	{
		$this->history()->empty();

		return $this;
	}


	// timeout
	// retourne l'objet timeout
	public function timeout():Timeout
	{
		return $this->get('timeout');
	}


	// timeoutEmpty
	// vide l'objet timeout
	public function timeoutEmpty():self
	{
		$timeout = $this->timeout();
		$timeout->empty();

		return $this;
	}


	// com
	// retourne l'objet com
	public function com():Com
	{
		return $this->get('com');
	}


	// comEmpty
	// vide l'objet com
	public function comEmpty():self
	{
		$this->com()->empty();

		return $this;
	}


	// flash
	// retourne l'objet session flash
	public function flash():Flash
	{
		return $this->get('flash');
	}


	// flashEmpty
	// vide l'objet flash
	public function flashEmpty():self
	{
		$this->flash()->empty();

		return $this;
	}


	// create_sid
	// crée une nouvelle clé de session
	// ne pas appelé directement, remplie une condition de SessionHandlerInterface
	public function create_sid():?string
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
	public function open($path,$name):bool
	{
		$return = false;
		$this->checkStarted();

		if(!is_string($name) || empty($name))
		static::throw('invalidName');

		if($path !== Base\Session::getSavePath())
		static::throw('invalidPath');

		$return = true;

		return $return;
	}


	// validateId
	// validate le id de session
	// ne pas appelé directement, remplie une condition de SessionHandlerInterface
	public function validateId($sid):bool
	{
		$return = false;
		$this->checkStarted();
		$prefix = Base\Session::getPrefix();
		$return = Base\Session::validateId($sid,$prefix);

		return $return;
	}


	// read
	// lit le contenu de la session
	// ne pas appelé directement, remplie une condition de SessionHandlerInterface
	public function read($sid):string
	{
		$return = '';
		$this->checkStarted();
		$this->checkSid($sid);
		$name = Base\Session::name();
		$class = $this->getStorageClass();
		$path = Base\Session::getSavePath(true);
		$this->storage = null;
		$storage = $class::sessionRead($path,$name,$sid);

		if(empty($storage))
		$storage = $class::sessionCreate($path,$name,$sid);

		if($storage instanceof Contract\Session)
		{
			$this->storage = $storage;
			$return = $storage->sessionData();
		}

		else
		static::throw('couldNotSelectOrInsert');

		return $return;
	}


	// write
	// écrit le contenu dans la session
	// ne pas appelé directement, remplie une condition de SessionHandlerInterface
	public function write($sid,$data):bool
	{
		$return = false;
		$this->checkStarted();
		$this->checkSid($sid);
		$return = $this->storage()->sessionWrite($data);

		return $return;
	}


	// updateTimestamp
	// met à jour le timestamp de la session, si les données n'ont pas changés
	// ne pas appelé directement, remplie une condition de SessionHandlerInterface
	public function updateTimestamp($sid,$data):bool
	{
		$return = false;
		$this->checkReady();
		$this->checkSid($sid);
		$return = $this->storage()->sessionUpdateTimestamp();

		return $return;
	}


	// close
	// ferme la session, doit retourner true
	// ne pas appelé directement, remplie une condition de SessionHandlerInterface
	// appele la méthode onEnd
	public function close():bool
	{
		$return = true;
		$this->checkStarted();
		$this->onEnd();

		return $return;
	}


	// destroy
	// tente de détruire la session
	// ne pas appelé directement, remplie une condition de SessionHandlerInterface
	public function destroy($sid):bool
	{
		$return = false;
		$this->checkReady();
		$this->checkSid($sid);
		$return = $this->storage()->sessionDestroy();

		return $return;
	}


	// gc
	// processus de garbage collect
	// ne pas appelé directement, remplie une condition de SessionHandlerInterface
	public function gc($lifetime):bool
	{
		$return = false;
		$this->checkReady();
		$class = $this->getStorageClass();
		$path = Base\Session::getSavePath(true);
		$name = Base\Session::name();
		$storage = $this->storage;

		$gc = $class::sessionGarbageCollect($path,$name,$lifetime,$storage);
		if(is_int($gc))
		$return = true;

		return $return;
	}
}
?>