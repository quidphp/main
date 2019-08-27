<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/base/blob/master/LICENSE
 */

namespace Quid\Main;
use Quid\Base;

// request
class Request extends Map
{
	// trait
	use _inst;
	use _option;
	use Map\_readOnly;


	// config
	public static $config = [
		'ipAllowed'=>[ // paramètre par défaut pour la méhode isIpAllowed
			'whiteList'=>null, // tableau de ip whiteList
			'blackList'=>null, // tableau de ip blackList
			'range'=>true, // comparaison range
			'level'=>null], // comparaison de niveau dans le ip
		'option'=>[ // options pour la requête
			'decode'=>false, // l'uri dans setUri est décodé
			'uri'=>null, // encode l'uri de sortie si utilise output, relative ou absolute
			'idLength'=>null, // longueur du id de la requête
			'safe'=>null, // option pour déterminer un path safe ou non
			'lang'=>null, // option pour lang
			'ping'=>2, // fait un ping avec curlExec
			'responseCode'=>null, // code de réponse souhaité lors de curlExec, sinon exception
			'timeout'=>10, // pour curl
			'dnsGlobalCache'=>false, // pour curl
			'userPassword'=>null, // pour curl
			'proxyHost'=>null, // hôte pour proxy, pour curl
			'proxyPort'=>8080, // port pour proxy, pour curl
			'proxyPassword'=>null, // pour curl
			'followLocation'=>false, // pour curl
			'ssl'=>null, // pour curl
			'port'=>null, // pour curl
			'sslCipher'=>null, // pour curl
			'userAgent'=>null, // pour curl
			'postJson'=>false], // le tableau post est encodé en json, pour curl
		'default'=>[ // ces défaut sont appliqués à chaque création d'objet
			'scheme'=>[Base\Request::class,'scheme'], // scheme de request par défaut
			'host'=>[Base\Request::class,'host'], // host de request par défaut
			'port'=>[Base\Request::class,'port'], // port de request par défaut
			'path'=>'/', // path par défaut
			'method'=>'get', // method par défaut
			'lang'=>[Base\Lang::class,'current'], // lang par défaut
			'ip'=>[Base\Server::class,'ip'], // ip du serveur par défaut
			'timestamp'=>[Base\Date::class,'getTimestamp']] // timestamp de date par défaut
	];


	// required
	protected static $required = ['id','scheme','host','path','method']; // propriété requise, ne peuvent pas être null


	// dynamique
	protected $id = null; // id unique de la requête
	protected $scheme = null; // scheme de la requête
	protected $user = null; // user de la requête
	protected $pass = null; // pass de la requête
	protected $host = null; // host de la requête
	protected $port = null; // port de la requête
	protected $path = null; // path relative de la requête
	protected $query = null; // query de la requête, en string
	protected $fragment = null; // fragment de la requête
	protected $method = null; // method de la requête
	protected $ip = null; // ip de la requête
	protected $headers = []; // headers de la requête
	protected $timestamp = null; // timestamp de la requête
	protected $lang = null; // lang de la requête, peut provenir de path
	protected $files = []; // contient les données de fichier
	protected $live = false; // défini si la requête représente la live
	protected $log = null; // permet de conserver des datas à logger


	// construct
	// construit l'objet requête
	// value peut être une string (uri) ou un tableau
	// si value est null, c'est la requête courante avec les options de base/request
	public function __construct($value=null,?array $option=null)
	{
		$live = false;
		if($value === null)
		{
			$live = true;
			$value = Base\Request::export(true,true);
		}

		$this->option($option);

		if(is_string($value))
		$this->setUri($value);

		elseif(is_array($value))
		$this->change($value);

		if(empty($this->id))
		$this->setId();

		$this->setDefault(true);
		$this->setLive($live);

		return;
	}


	// invoke
	// retourne une des propriétés de l'objet via une des méthodes get
	public function __invoke(...$args)
	{
		$return = null;

		if(!empty($args))
		{
			$key = current($args);

			if(is_string($key) && property_exists($this,$key) && method_exists($this,$key))
			$return = $this->$key();

			else
			static::throw('invalid',$key);
		}

		return $return;
	}


	// toString
	// retourne la méthode output cast en string
	public function __toString():string
	{
		return Base\Str::cast($this->output());
	}


	// onSetInst
	// méthode appeler après setInst
	// méthode protégé
	protected function onSetInst():self
	{
		return $this->readOnly(true);
	}


	// onUnsetInst
	// méthode appeler après unsetInst
	// méthode protégé
	protected function onUnsetInst():self
	{
		return $this->readOnly(false);
	}


	// cast
	// retourne la valeur cast, la méthode output
	public function _cast():string
	{
		return $this->output();
	}


	// toArray
	// retourne tout l'objet sous forme de tableau
	public function toArray():array
	{
		return get_object_vars($this);
	}


	// jsonSerialize
	// json serialize la requête avec le id
	// retourne save info
	public function jsonSerialize():array
	{
		$this->checkAllowed('jsonSerialize');
		return $this->safeInfo(true);
	}


	// isLive
	// retourne vrai si la requête est live
	public function isLive():bool
	{
		return $this->live;
	}


	// setLive
	// genre la valeur à la propriété live
	// méthode protégé
	protected function setLive(bool $value):self
	{
		$this->live = $value;

		return $this;
	}


	// getLogData
	// retourne les data pour le log
	public function getLogData():?array
	{
		return $this->log;
	}


	// setLog
	// permet d'attributer des datas au log
	public function setLogData(?array $value):self
	{
		$this->log = $value;

		return $this;
	}


	// getOptionBase
	// permet d'obtenir une option merge avec les config dans base/request
	public function getOptionBase(string $key)
	{
		$return = $this->getOption($key);
		$base = Base\Request::$config[$key] ?? null;

		if($base !== null)
		{
			if($return === null)
			$return = $base;

			elseif(is_array($return))
			$return = Base\Arrs::replace($base,$return);
		}

		return $return;
	}


	// setDefault
	// applique les défauts à la requête
	// si fill est true, ne remplace pas les valeurs non null
	public function setDefault(bool $fill=true):self
	{
		if($fill === true)
		{
			$change = [];
			foreach (static::default() as $key => $value)
			{
				if($this->$key === null)
				$change[$key] = $value;
			}
		}

		else
		$change = static::default();

		if(!empty($change))
		$this->change($change);

		return $this->checkRequired();
	}


	// str
	// envoie info à http str qui retourne une string pour représenter la requête
	public function str(?array $option=null):string
	{
		return Base\Http::str($this->info(),$option);
	}


	// uri
	// construit l'uri à partir du tableau parse
	// n'est pas encodé ou décodé, plus rapide que les autres méthodes
	// absolut est true si le host de la requête n'est pas celui courant
	public function uri(?bool $absolute=null):string
	{
		$return = '';

		if($absolute === null)
		$absolute = ($this->isSchemeHostCurrent())? false:true;

		if($absolute === true)
		$parse = $this->parse();
		else
		$parse = ['path'=>$this->path(),'query'=>$this->query(),'fragment'=>$this->fragment()];

		$return = Base\Uri::build($parse);

		return $return;
	}


	// output
	// retourne l'uri, peut être relatif ou absolut dépendamment des options uri
	public function output(?array $option=null):?string
	{
		return Base\Uri::output($this->uri(true),Base\Arr::plus($this->getOption('uri'),$option));
	}


	// relative
	// retourne l'uri relative de la requête
	public function relative(?array $option=null):?string
	{
		return Base\Uri::relative($this->uri(false),Base\Arr::plus($this->getOption('uri'),$option));
	}


	// relativeExists
	// retourne l'uri relative de la requête, base uri va vérifier qu'elle existe
	public function relativeExists(?array $option=null)
	{
		return Base\Uri::relative($this->uri(false),Base\Arr::plus($this->getOption('uri'),$option,['exists'=>true]));
	}


	// absolute
	// retourne l'uri absolue de la requête
	public function absolute(?array $option=null):?string
	{
		return Base\Uri::absolute($this->uri(true),null,Base\Arr::plus($this->getOption('uri'),$option));
	}


	// setUri
	// change l'uri de la requête
	// plusieurs paramètres peuvent être pris de l'uri, pas seulement le path
	// par exemple host, user, pass, port, query et fragment
	// par l'option par défaut, l'uri est décodé
	public function setUri(string $value,?bool $decode=null):self
	{
		$parse = Base\Uri::parse($value,$decode ?? $this->getOption('decode'));

		if(!empty($parse))
		{
			$change = Base\Arr::cleanNull($parse);
			$this->change($change);
		}

		return $this;
	}


	// setAbsolute
	// alias de setUri
	public function setAbsolute(string $value,?bool $decode=null):self
	{
		return $this->setUri($value,$decode);
	}


	// info
	// retourne un tableau complet à partir de la requête
	// possible d'exporter le id
	public function info(bool $id=false):array
	{
		$return = $this->parse();

		$return['relative'] = $this->relative();
		$return['absolute'] = $this->absolute();
		$return['schemeHost'] = $this->schemeHost();
		$return['method'] = $this->method();
		$return['ajax'] = $this->isAjax();
		$return['ssl'] = $this->isSsl();
		$return['timestamp'] = $this->timestamp();
		$return['ip'] = $this->ip();
		$return['get'] = $this->queryArray();
		$return['post'] = $this->post();
		$return['files'] = $this->files();
		$return['headers'] = $this->headers();
		$return['userAgent'] = $this->userAgent();
		$return['referer'] = $this->referer();
		$return['lang'] = $this->lang();
		$return['safe'] = $this->isPathSafe();
		$return['cachable'] = $this->isCachable();
		$return['redirectable'] = $this->isRedirectable();

		if($id === true)
		$return['id'] = $this->id();

		return $return;
	}


	// safeInfo
	// exporte les informations de la requête, utiliser par Tablelog
	// similaire à info pour mais post et headers incluent seulement les clés et true comme valeur
	// possible d'exporter le id
	public function safeInfo(bool $id=false):array
	{
		$return = $this->parse();

		$return['absolute'] = $this->absolute();
		$return['method'] = $this->method();
		$return['ajax'] = $this->isAjax();
		$return['ssl'] = $this->isSsl();
		$return['timestamp'] = $this->timestamp();
		$return['ip'] = $this->ip();
		$return['post'] = Base\Arr::combine(array_keys($this->post()),true);
		$return['files'] = Base\Arr::combine(array_keys($this->files()),true);
		$return['headers'] = Base\Arr::combine(array_keys($this->headers()),true);
		$return['lang'] = $this->lang();
		$return['userAgent'] = $this->userAgent();
		$return['referer'] = $this->referer();

		if($id === true)
		$return['id'] = $this->id();

		return $return;
	}


	// export
	// exporte les informations liés à la requête courante
	// possible d'exporter le id
	public function export(bool $id=false):array
	{
		$return = $this->parse();
		$return['method'] = $this->method();
		$return['timestamp'] = $this->timestamp();
		$return['ip'] = $this->ip();
		$return['post'] = $this->post();
		$return['files'] = $this->files();
		$return['headers'] = $this->headers();
		$return['lang'] = $this->lang();

		if($id === true)
		$return['id'] = $this->id();

		return $return;
	}


	// parse
	// retourne le tableau parse de l'objet
	public function parse():array
	{
		$return = [];
		$return['scheme'] = $this->scheme();
		$return['user'] = $this->user();
		$return['pass'] = $this->pass();
		$return['host'] = $this->host();
		$return['port'] = $this->port();
		$return['path'] = $this->path();
		$return['query'] = $this->query();
		$return['fragment'] = $this->fragment();

		return $return;
	}


	// change
	// remplace des valeurs d'éléments de la requête
	// ces valeurs sont appliqués tel quel et ne sont pas décodés
	public function change(array $value):self
	{
		foreach ($value as $key => $value)
		{
			if(is_string($key))
			{
				$method = 'set'.ucfirst($key);

				if(method_exists($this,$method))
				$this->$method($value);
			}
		}

		return $this;
	}


	// property
	// fait un changement sur une propriété
	// si requête est live et liveBase est true, renvoie à base/request pour faire le changement la aussi
	// fait le checkReadOnly
	protected function property(string $key,$value,bool $liveBase=true):self
	{
		$this->checkReadOnly();
		$this->$key = $value;

		if($this->isLive() && $liveBase === true && $key !== 'id')
		{
			if($key === 'lang')
			$key = 'langHeader';

			$method = 'set'.ucfirst($key);
			Base\Request::$method($value);
		}

		return $this;
	}


	// isSsl
	// retourne vrai si la requête est ssl
	public function isSsl():bool
	{
		return Base\Http::ssl($this->scheme);
	}


	// isAjax
	// retourne vrai si la requête est ajax
	public function isAjax():bool
	{
		return ($this->header('X-Requested-With') === 'XMLHttpRequest')? true:false;
	}


	// isGet
	// retourne vrai si la requête est get
	public function isGet():bool
	{
		return ($this->method() === 'get')? true:false;
	}


	// isPost
	// retourne vrai si la requête est post
	public function isPost():bool
	{
		return ($this->method() === 'post')? true:false;
	}


	// isPostWithoutData
	// retourne vrai si la requête est post mais qu'il n'y a pas de données post
	// ceci peut arriver lors du chargement d'un fichier plus lourd que php ini
	public function isPostWithoutData():bool
	{
		return ($this->isPost() && empty($this->post()))? true:false;
	}


	// isRefererInternal
	// retourne vrai si le referrer est interne (donc même host que le courant)
	// possible de fournir un tableau d'autres hosts considérés comme internal
	public function isRefererInternal($hosts=null):bool
	{
		return (!empty($this->referer(true,$hosts)))? true:false;
	}


	// isInternalPost
	// retourne vrai si la requête semble être un post avec un referer provenant du même domaine
	// possible de fournir un tableau d'autres hosts considérés comme internal
	public function isInternalPost($hosts=null):bool
	{
		return ($this->isPost() && $this->isRefererInternal($hosts))? true:false;
	}


	// isExternalPost
	// retourne vrai si la requête semble être un post avec un referer provenant d'un autre domaine
	// possible de fournir un tableau d'autres hosts considérés comme internal
	public function isExternalPost($hosts=null):bool
	{
		return ($this->isPost() && !$this->isRefererInternal($hosts))? true:false;
	}


	// isStandard
	// retourne vrai si la requête est de méthode get et pas ajax
	public function isStandard():bool
	{
		return ($this->isGet() && !$this->isAjax())? true:false;
	}


	// isPathEmpty
	// retourne vrai si le chemin de la requête est vide (ou seulement /)
	public function isPathEmpty():bool
	{
		return ($this->path(true) === '')? true:false;
	}


	// isPathMatchEmpty
	// retourne vrai si le chemin match de la requête est vide (ou seulement /)
	public function isPathMatchEmpty():bool
	{
		return ($this->pathMatch() === '')? true:false;
	}


	// isSelected
	// retourne vrai si la requête est sélectionné dans base attr
	public function isSelected():bool
	{
		return Base\Attr::isSelectedUri($this->uri());
	}


	// isCachable
	// retourne vrai si la requête est cachable
	// ce doit être une requête de méthode get, sans post et avec chemin sécuritaire
	public function isCachable():bool
	{
		return ($this->isGet() && !$this->hasPost() && $this->isPathSafe())? true:false;
	}


	// isRedirectable
	// retourne vrai si la requête est redirectable
	// ce doit être une requête de méthode get, sans post et avec chemin sécuritaire
	public function isRedirectable():bool
	{
		return ($this->isGet() && !$this->hasPost() && $this->isPathSafe())? true:false;
	}


	// isFailedFileUpload
	// retourne vrai si la requête semble être un envoie de fichier raté
	// doit être live
	public function isFailedFileUpload():bool
	{
		return ($this->isLive() && $this->isPostWithoutData() && Base\Superglobal::hasServerLengthWithoutPost())? true:false;
	}


	// hasExtension
	// retourne vrai si la requête a une extension
	public function hasExtension():bool
	{
		return Base\Path::hasExtension($this->path());
	}


	// hasQuery
	// retourne vrai si la requête a un query
	public function hasQuery():bool
	{
		return (!empty($this->query))? true:false;
	}


	// hasLang
	// retourne vrai si la requête a une lang
	public function hasLang():bool
	{
		return (!empty($this->lang))? true:false;
	}


	// hasPost
	// retourne vrai si la requête courante contient des données post
	public function hasPost():bool
	{
		return (!empty($this->post()))? true:false;
	}


	// hasData
	// retourne vrai si la requête courante contient des données get ou post
	public function hasData():bool
	{
		return ($this->hasQuery() || $this->hasPost())? true:false;
	}


	// hasEmptyGenuine
	// retourne vrai si post contient la clé genuine et le contenu est vide
	public function hasEmptyGenuine():bool
	{
		$return = false;
		$post = $this->post();
		$genuine = Base\Html::getGenuineName();

		if(!empty($genuine) && !empty($post) && array_key_exists($genuine,$post) && empty($post['genuine']))
		$return = true;

		return $return;
	}


	// hasUser
	// retourne vrai si la requête courante contient un user
	public function hasUser():bool
	{
		return (is_string($this->user()))? true:false;
	}


	// hasPass
	// retourne vrai si la requête courante contient un pass
	public function hasPass():bool
	{
		return (is_string($this->pass()))? true:false;
	}


	// hasFragment
	// retourne vrai si la requête courante contient un fragment
	public function hasFragment():bool
	{
		return (is_string($this->fragment()))? true:false;
	}


	// hasIp
	// retourne vrai si la requête courante contient un ip
	public function hasIp():bool
	{
		return (!empty($this->ip()))? true:false;
	}


	// hasUserAgent
	// retourne vrai si la requête courante contient un userAgent
	public function hasUserAgent():bool
	{
		return (!empty($this->userAgent()))? true:false;
	}


	// hasHeaders
	// retourne vrai si le tableau headers n'est pas vide
	public function hasHeaders():bool
	{
		return (!empty($this->headers()))? true:false;
	}


	// isHeader
	// retourne vrai si la ou les clés headers existent
	public function isHeader(...$keys):bool
	{
		return Base\Header::exists($keys,$this->headers());
	}


	// isDesktop
	// retourne vrai si le userAgent est desktop
	public function isDesktop():bool
	{
		return Base\Browser::isDesktop($this->userAgent())? true:false;
	}


	// isMobile
	// retourne vrai si le userAgent est mobile
	public function isMobile():bool
	{
		return Base\Browser::isMobile($this->userAgent())? true:false;
	}


	// isOldIe
	// retourne vrai si le userAgent est Internet Explorer < 9
	public function isOldIe():bool
	{
		return Base\Browser::isOldIe($this->userAgent())? true:false;
	}


	// isMac
	// retourne vrai si le userAgent est sur MacOs
	public function isMac():bool
	{
		return Base\Browser::isMac($this->userAgent())? true:false;
	}


	// isLinux
	// retourne vrai si le userAgent est sur Linux
	public function isLinux():bool
	{
		return Base\Browser::isLinux($this->userAgent())? true:false;
	}


	// isWindows
	// retourne vrai si le userAgent est sur Windows
	public function isWindows():bool
	{
		return Base\Browser::isWindows($this->userAgent())? true:false;
	}


	// isBot
	// retourne vrai si le userAgent est un bot
	public function isBot():bool
	{
		return Base\Browser::isBot($this->userAgent())? true:false;
	}


	// isInternal
	// retourne vrai si la requête est interne, même host que la requête courante
	public function isInternal():bool
	{
		return Base\Uri::isInternal($this->uri());
	}


	// isExternal
	// retourne vrai si la requête est externe, host différent que requête courante
	public function isExternal():bool
	{
		return Base\Uri::isExternal($this->uri());
	}


	// isScheme
	// retourne vrai si la requête a un scheme du type spécifié
	public function isScheme($value):bool
	{
		return Base\Uri::isScheme($value,$this->uri());
	}


	// isHost
	// retourne vrai si la requête a l'hôte spécifié
	public function isHost($value):bool
	{
		return Base\Uri::isHost($value,$this->uri());
	}


	// isSchemeHost
	// retourne vrai si la requête a le schemeHost spécifié
	public function isSchemeHost($value):bool
	{
		return Base\Uri::isSchemeHost($value,$this->uri());
	}


	// isExtension
	// retourne vrai si la requête à une extension du type
	// possibilité de mettre une ou plusieurs target
	public function isExtension(...$values):bool
	{
		return Base\Uri::isExtension($values,$this->uri());
	}


	// isQuery
	// retourne vrai si l'uri a des query en argument
	// possibilité de mettre une ou plusieurs valeurs
	public function isQuery(...$values):bool
	{
		return Base\Uri::isQuery($values,$this->uri());
	}


	// isLang
	// retourne vrai si la requête a la langue spécifié
	public function isLang($value):bool
	{
		return Base\Path::isLang($value,$this->path());
	}


	// isIp
	// retourne vrai si le ip est celui fourni
	public function isIp($value):bool
	{
		return (is_string($value) && $value === $this->ip())? true:false;
	}


	// isIpLocal
	// retourne vrai si le ip est local
	public function isIpLocal():bool
	{
		return ($this->hasIp() && Base\Ip::isLocal($this->ip()))? true:false;
	}


	// isIpAllowed
	// retourne vrai si le ip est permis en fonction du whitelist et blacklist gardé dans les config de la classe
	public function isIpAllowed():bool
	{
		return ($this->hasIp() && Base\Ip::allowed($this->ip(),static::$config['ipAllowed']))? true:false;
	}


	// isPathSafe
	// retourne vrai si le path est considéré comme safe
	public function isPathSafe():bool
	{
		return Base\Path::isSafe($this->path(),$this->getOptionBase('safe'));
	}


	// hasFiles
	// retourne vrai si la requête contient des fichiers
	public function hasFiles():bool
	{
		return (!empty($this->files))? true:false;
	}


	// checkPathSafe
	// envoie une exception si le chemin n'est pas safe
	public function checkPathSafe():self
	{
		if(!$this->isPathSafe())
		static::throw();

		return $this;
	}


	// checkRequired
	// lance une exception si une des propriétés requises est null
	public function checkRequired():self
	{
		foreach (static::$required as $key)
		{
			if($this->$key === null)
			static::throw($key,'cannotBeNull');
		}

		return $this;
	}


	// checkAfter
	// lancé après chaque méthode en écriture, cast le tableau post
	public function checkAfter():parent
	{
		$this->data = Base\Arrs::cast($this->data);

		return $this;
	}


	// setId
	// change le id unique de la requête
	// si value est null, génère un id unique en utilisant la config id length
	public function setId(?string $value=null):self
	{
		if(empty($value))
		$value = Base\Str::random($this->getOptionBase('idLength'));

		$this->property('id',$value);

		return $this;
	}


	// id
	// retourne le id unique de la requête
	public function id():string
	{
		return $this->id;
	}


	// scheme
	// retourne le scheme courant de la requête
	public function scheme():?string
	{
		return $this->scheme;
	}


	// setScheme
	// change le scheme courant de la requête
	// change aussi le port
	// value ne peut pas être null
	public function setScheme(string $value):self
	{
		if(Base\Uri::isSchemeValid($value))
		{
			$this->property('scheme',$value);

			$port = Base\Http::port($value);
			if(is_int($port) && $port !== $this->port)
			$this->setPort($port);
		}

		else
		static::throw('unsupportedScheme');

		return $this;
	}


	// user
	// retourne le user de la requête
	public function user():?string
	{
		return $this->user;
	}


	// setUser
	// change le user de la requête
	// value peut être null
	public function setUser(?string $value):self
	{
		return $this->property('user',$value);
	}


	// pass
	// retourne le pass de la requête
	public function pass():?string
	{
		return $this->pass;
	}


	// setPass
	// change le pass de la requête
	// value peut être null
	public function setPass(?string $value):self
	{
		return $this->property('pass',$value);
	}


	// host
	// retourne le host de la requête
	public function host():?string
	{
		return $this->host;
	}


	// setHost
	// change le host de la requête
	// value ne peut pas être null
	public function setHost(string $value):self
	{
		if(Base\Http::isHost($value))
		$this->property('host',$value);

		else
		static::throw();

		return $this;
	}


	// isSchemeHostCurrent
	// retourne vrai si le scheme host est celui de la requête courante
	// passe dans base/request
	public function isSchemeHostCurrent():bool
	{
		return Base\Request::isSchemeHost($this->schemeHost());
	}


	// port
	// retourne le port de la requête
	public function port():?int
	{
		return $this->port;
	}


	// setPort
	// change le port de la requête
	// change aussi le scheme
	// value peut être null
	public function setPort(?int $value):self
	{
		$this->property('port',$value);

		$scheme = Base\Http::scheme($value);
		if(!empty($scheme) && $scheme !== $this->scheme)
		$this->setScheme($scheme);

		return $this;
	}


	// path
	// retourne le chemin de la requête
	// si stripStart est true, enlève le slash du début
	public function path(bool $stripStart=false):?string
	{
		$return = $this->path;

		if($stripStart === true)
		$return = Base\Path::stripStart($return);

		return $return;
	}


	// setPath
	// change le chemin de la requête
	// value ne peut pas être null
	// si le path a une langue, envoie dans setLang
	public function setPath(string $value):self
	{
		$this->property('path',$value);

		$lang = Base\Path::lang($value,$this->getOptionBase('lang'));
		if(!empty($lang))
		$this->setLang($lang);

		return $this;
	}


	// pathStripStart
	// retourne le path de la requête sans le séparateur au début
	public function pathStripStart():?string
	{
		return $this->path(true);
	}


	// pathinfo
	// retourne le tableau pathinfo de la requête
	// peut aussi retourner une seule variable pathinfo
	public function pathinfo(?int $key=null)
	{
		return Base\Path::info($this->uri(),$key);
	}


	// changePathinfo
	// change un ou plusieurs éléments du pathinfo de la requête
	public function changePathinfo(array $change):self
	{
		return $this->setPath(Base\Path::change($change,$this->path()));
	}


	// keepPathinfo
	// garde un ou plusieurs éléments du pathinfo de la requête
	public function keepPathinfo(array $change):self
	{
		return $this->setPath(Base\Path::keep($change,$this->path()));
	}


	// removePathinfo
	// enlève un ou plusieurs éléments du pathinfo de la requête
	public function removePathinfo(array $change):self
	{
		return $this->setPath(Base\Path::remove($change,$this->path()));
	}


	// dirname
	// retourne le dirname du path de la requête
	public function dirname():?string
	{
		return Base\Path::dirname($this->path());
	}


	// addDirname
	// ajoute un dirname après le dirname du path de la requête
	public function addDirname(string $change):self
	{
		return $this->setPath(Base\Path::addDirname($change,$this->path()));
	}


	// changeDirname
	// change le dirname du path de la requête
	public function changeDirname(string $change):self
	{
		return $this->setPath(Base\Path::changeDirname($change,$this->path()));
	}


	// removeDirname
	// enlève un dirname au path de la requête
	public function removeDirname():self
	{
		return $this->setPath(Base\Path::removeDirname($this->path()));
	}


	// basename
	// retourne le basename du path de la requête
	public function basename():?string
	{
		return Base\Path::basename($this->path());
	}


	// addBasename
	// ajoute un basename après le dirname du path de la requête
	public function addBasename(string $change):self
	{
		return $this->setPath(Base\Path::addBasename($change,$this->path()));
	}


	// changeBasename
	// change le basename du path de la requête
	public function changeBasename(string $change):self
	{
		return $this->setPath(Base\Path::changeBasename($change,$this->path()));
	}


	// removeBasename
	// enlève le basename du path de la requête
	public function removeBasename():self
	{
		return $this->setPath(Base\Path::removeBasename($this->path()));
	}


	// filename
	// retourne le filename du path de la requête
	public function filename():?string
	{
		return Base\Path::filename($this->path());
	}


	// addFilename
	// ajoute un filename après le dirname du path de la requête
	public function addFilename(string $change):self
	{
		return $this->setPath(Base\Path::addFilename($change,$this->path()));
	}


	// changeFilename
	// change le filename du path de la requête
	public function changeFilename(string $change):self
	{
		return $this->setPath(Base\Path::changeFilename($change,$this->path()));
	}


	// removeFilename
	// enlève un filename du path de la requête
	public function removeFilename():self
	{
		return $this->setPath(Base\Path::removeFilename($this->path()));
	}


	// extension
	// retourne l'extension du path de la requête
	public function extension():?string
	{
		return Base\Path::extension($this->path());
	}


	// addExtension
	// ajoute l'extension après le dirname du path de la requête
	public function addExtension(string $change):self
	{
		return $this->setPath(Base\Path::addExtension($change,$this->path()));
	}


	// changeExtension
	// change l'extension du path de la requête
	public function changeExtension(string $change):self
	{
		return $this->setPath(Base\Path::changeExtension($change,$this->path()));
	}


	// removeExtension
	// enlève une extension au path de la requête
	public function removeExtension():self
	{
		return $this->setPath(Base\Path::removeExtension($this->path()));
	}


	// mime
	// retourne le mimetype du path de la requête à partir de son extension
	// ne vérifie pas l'existence du fichier
	public function mime():?string
	{
		return Base\Path::mime($this->path());
	}


	// addLang
	// ajoute un code de langue au path de la requête
	// ajoute même si le code existe déjà
	// le path sera retourné vide si le code langue est invalide
	public function addLang(string $change):self
	{
		return $this->setPath(Base\Path::addLang($change,$this->path()));
	}


	// changeLang
	// ajoute ou remplace un code de langue au path de la requête
	// le path sera retourné vide si le code langue est invalide
	public function changeLang(string $change):self
	{
		return $this->setPath(Base\Path::changeLang($change,$this->path()));
	}


	// removeLang
	// enlève un code de langue au path de la requête
	// retourne le chemin dans tous les cas
	public function removeLang():self
	{
		return $this->setPath(Base\Path::removeLang($this->path()));
	}


	// pathPrepend
	// prepend un path derrière le path de la requête
	public function pathPrepend(string $value):self
	{
		return $this->setPath(Base\Path::prepend($this->path(true),$value));
	}


	// pathAppend
	// append un path devant le path de la requête
	public function pathAppend(string $value):self
	{
		return $this->setPath(Base\Path::append($this->path(true),$value));
	}


	// pathExplode
	// explode le path de la requête
	public function pathExplode():array
	{
		return Base\Path::arr($this->path(true));
	}


	// pathGet
	// retourne un index du path de la requête
	public function pathGet(int $index):?string
	{
		return Base\Path::get($index,$this->path(true));
	}


	// pathCount
	// count le nombre de niveau dans le path de la requête
	public function pathCount():int
	{
		return Base\Path::count($this->path(true));
	}


	// pathSlice
	// tranche des slices du path de la requête en utilisant offset et length
	public function pathSlice(int $offset,?int $length):array
	{
		return Base\Path::slice($offset,$length,$this->path(true));
	}


	// pathSplice
	// efface et remplace des slices du path de la requête en utilisant offset et length
	public function pathSplice(int $offset,?int $length,$replace=null):self
	{
		return $this->setPath(Base\Path::splice($offset,$length,$this->path(true),$replace));
	}


	// pathInsert
	// ajoute un ou plusieurs éléments dans le path sans ne rien effacer
	public function pathInsert(int $offset,$replace):self
	{
		return $this->setPath(Base\Path::insert($offset,$replace,$this->path(true)));
	}


	// pathMatch
	// retourne le chemin sans le code de langue et sans le wrap du début
	public function pathMatch():string
	{
		return Base\Path::match($this->path(),$this->getOptionBase('lang'));
	}


	// query
	// retourne la query string de la requête
	public function query():?string
	{
		return $this->query;
	}


	// setQuery
	// change la query string de la requête
	// accepte un tableau en argument
	// value peut être null
	public function setQuery($value):self
	{
		if(is_array($value))
		$value = Base\Uri::buildQuery($value,false);

		if(is_string($value) || $value === null)
		$this->property('query',$value);

		else
		static::throw();

		return $this;
	}


	// queryArray
	// retourne la query string de la requête sous forme de tableau
	public function queryArray():array
	{
		return (is_string($this->query))? Base\Uri::parseQuery($this->query):[];
	}


	// getQuery
	// retourne la valeur d'une clé get query
	public function getQuery($key)
	{
		return Base\Arr::get($key,$this->queryArray());
	}


	// getsQuery
	// retournes plusieurs valeurs dans query
	public function getsQuery(...$keys):array
	{
		return Base\Arr::gets($keys,$this->queryArray());
	}


	// addQuery
	// insert ou update une valeur dans query
	public function addQuery($key,$value):self
	{
		return $this->setQuery(Base\Uri::buildQuery(Base\Arr::set($key,$value,$this->queryArray())));
	}


	// setsQuery
	// insert ou update une ou plusieurs valeurs dans query
	public function setsQuery(array $values):self
	{
		return $this->setQuery(Base\Uri::buildQuery(Base\Arr::sets($values,$this->queryArray())));
	}


	// unsetQuery
	// enlève une ou plusieurs clés dans query
	public function unsetQuery(...$keys):self
	{
		return $this->setQuery(Base\Uri::buildQuery(Base\Arr::unsets($keys,$this->queryArray())));
	}


	// fragment
	// retourne le fragment de la requête
	public function fragment():?string
	{
		return $this->fragment;
	}


	// setFragment
	// change le fragment de la requête
	// value peut être null
	public function setFragment(?string $value):self
	{
		return $this->property('fragment',$value);
	}


	// lang
	// retourne la langue de la requête
	// la langue est automatiquement considéré à partir du path
	public function lang():?string
	{
		return $this->lang;
	}


	// setLang
	// change la langue de la requête, créer aussi le header accept-language
	// value peut être null
	public function setLang(?string $value,bool $header=true):self
	{
		$value = Base\Lang::prepareCode($value);

		if($header === true)
		{
			$langHeader = $this->langHeader();

			if(is_string($value) && is_string($langHeader) && strpos($langHeader,$value) !== false)
			$header = false;

			if($header === true)
			$this->setLangHeader($value);
		}

		$this->property('lang',$value,$header);

		return $this;
	}


	// langHeader
	// retourne la valeur du header lang de la requête
	public function langHeader()
	{
		return $this->header('Accept-Language');
	}


	// setLangHeader
	// change le accept-language de la requête dans les headers
	// value peut être null
	public function setLangHeader(?string $value):self
	{
		$this->setHeader('Accept-Language',$value);

		return $this;
	}


	// schemeHost
	// retourne le scheme et host de l'uri de la requête
	public function schemeHost():string
	{
		return Base\Uri::schemeHost($this->uri(true));
	}


	// schemeHostPath
	// retourne le scheme, domaine et path de l'uri de la requête
	public function schemeHostPath():string
	{
		return Base\Uri::schemeHostPath($this->uri(true));
	}


	// hostPath
	// retourne le domaine et path de l'uri de la requête
	public function hostPath():string
	{
		return Base\Uri::hostPath($this->uri(true));
	}


	// pathQuery
	// retourne le path et la query de la requête
	public function pathQuery():string
	{
		return Base\Uri::pathQuery($this->uri(true));
	}


	// method
	// retourne la méthode de la requête
	public function method():?string
	{
		return $this->method;
	}


	// setMethod
	// change la méthode de la requête
	// value ne peut pas être null
	public function setMethod(string $value):self
	{
		if(Base\Http::isMethod($value))
		$this->property('method',strtolower($value));

		else
		static::throw();

		return $this;
	}


	// setAjax
	// change la valeur ajax de la requête dans les headers
	// value doit être bool
	public function setAjax(bool $value):self
	{
		if($value === true)
		$this->setHeader('X-Requested-With','XMLHttpRequest');
		else
		$this->unsetHeader('X-Requested-With');

		return $this;
	}


	// setSsl
	// change la valeur ssl de la requête, donc change le scheme
	// value doit être bool
	public function setSsl(bool $value):self
	{
		$this->setScheme(Base\Http::scheme($value));

		return $this;
	}


	// post
	// retourne le tableau post de la requête, si existant
	// utilise base/superglobal postReformat
	// possibilité d'enlever les clés de post qui ne sont pas des noms de colonnes ou nom de clés réservés
	// possibilité d'enlever les tags html dans le tableau de retour
	// possibilité d'inclure les données chargés en provenance de files comme variable post
	// les données de files sont reformat par défaut, mais post a toujours précédente sur files
	public function post(bool $safeKey=false,bool $stripTags=false,bool $includeFiles=false):array
	{
		return Base\Superglobal::postReformat($this->arr(),$safeKey,$stripTags,$includeFiles,$this->files());
	}


	// postJson
	// retourne le tableau post de la requête sous forme de json
	public function postJson(bool $onlyCol=false,bool $stripTags=false):string
	{
		return Base\Json::encode($this->post($onlyCol,$stripTags));
	}


	// postQuery
	// retourne le tableau post de la requête sous forme de query
	public function postQuery(bool $onlyCol=false,bool $stripTags=false,bool $encode=true):?string
	{
		return Base\Uri::buildQuery($this->post($onlyCol,$stripTags),$encode);
	}


	// csrf
	// retourne la chaîne csrf de la requête
	public function csrf():?string
	{
		$return = null;
		$name = Base\Session::getCsrfName();
		$attr = Base\Session::getCsrfOption();

		if(is_string($name) && !empty($attr['length']))
		{
			$csrf = $this->get($name);

			if(!empty($csrf) && is_string($csrf) && strlen($csrf) === $attr['length'])
			$return = $csrf;
		}

		return $return;
	}


	// captcha
	// retourne la chaîne captcha de la requête
	public function captcha():?string
	{
		$return = null;
		$name = Base\Session::getCaptchaName();

		if(is_string($name))
		{
			$captcha = $this->get($name);

			if(!empty($captcha) && is_string($captcha))
			$return = $captcha;
		}

		return $return;
	}


	// setPost
	// change le tableau post de la requête
	// si le tableau n'est pas vide, mais la méthode post par défaut
	// value doit absolument être array
	public function setPost(array $value):self
	{
		if(!empty($value))
		$this->setMethod('post');

		$return = $this->overwrite($value);

		return $return;
	}


	// ip
	// retourne le ip de la requête
	public function ip():?string
	{
		return $this->ip;
	}


	// setIp
	// change le ip de la requête
	// value peut être null
	public function setIp(?string $value):self
	{
		if($value === null || Base\Ip::is($value))
		$this->property('ip',$value);

		else
		static::throw();

		return $this;
	}


	// userAgent
	// retourne le userAgent de la requête
	public function userAgent()
	{
		return $this->header('User-Agent');
	}


	// setUserAgent
	// change le userAgent de la requête dans les headers
	// value peut être null
	public function setUserAgent(?string $value):self
	{
		$this->setHeader('User-Agent',$value);

		return $this;
	}


	// referer
	// retourne l'uri référent à la requête
	// possible de retourner seulement si le referer est interne (et possible de spécifier un tableau d'host considéré comme interne)
	public function referer(bool $internal=false,$hosts=null):?string
	{
		$return = null;
		$referer = $this->header('Referer');

		if(is_string($referer) && !empty($referer))
		{
			if($internal === false || Base\Uri::isInternal($referer,$hosts))
			$return = $referer;
		}

		return $return;
	}


	// setReferer
	// change le setReferer de la requête dans les headers
	// value peut être null
	public function setReferer(?string $value):self
	{
		$this->setHeader('Referer',$value);

		return $this;
	}


	// timestamp
	// retourne le timestamp de la requête
	public function timestamp():?int
	{
		return $this->timestamp;
	}


	// setTimestamp
	// change le timestamp de la requête
	// value peut être null
	public function setTimestamp(?int $value):self
	{
		return $this->property('timestamp',$value);
	}


	// headers
	// retourne les headers de la requête
	public function headers():array
	{
		return $this->headers;
	}


	// header
	// retourne un header de la requête
	public function header(string $key)
	{
		return Base\Header::get($key,$this->headers());
	}


	// setHeaders
	// remplace les headers
	// value doit être un tableau
	public function setHeaders(array $values):self
	{
		$this->property('headers',[]);
		$this->addHeaders($values);

		return $this;
	}


	// addHeaders
	// ajoute les headers à ceux existant
	public function addHeaders(array $values):self
	{
		foreach ($values as $key => $value)
		{
			if(is_string($key))
			$this->setHeader($key,$value);
		}

		return $this;
	}


	// setHeader
	// ajoute ou change un header
	public function setHeader(string $key,$value):self
	{
		return $this->property('headers',Base\Header::set($key,$value,$this->headers()));
	}


	// unsetHeader
	// enlève un ou plusieurs headers
	public function unsetHeader(...$keys):self
	{
		return $this->property('headers',Base\Header::unsets($keys,$this->headers()));
	}


	// fingerprint
	// retourne le fingerprint des headers
	public function fingerprint(array $keys):?string
	{
		return Base\Header::fingerprint($this->headers(),$keys);
	}


	// browserCap
	// retourne les capacités du browser en fonction du userAgent
	public function browserCap():?array
	{
		return (is_string($userAgent = $this->userAgent()))? Base\Browser::cap($userAgent):null;
	}


	// browserName
	// retourne le nom du browser du userAgent
	public function browserName():?string
	{
		return (is_string($userAgent = $this->userAgent()))? Base\Browser::name($userAgent):null;
	}


	// browserPlatform
	// retourne la plateforme du browser du userAgent
	public function browserPlatform():?string
	{
		return (is_string($userAgent = $this->userAgent()))? Base\Browser::platform($userAgent):null;
	}


	// browserDevice
	// retourne le device du browser du userAgent
	public function browserDevice():?string
	{
		return (is_string($userAgent = $this->userAgent()))? Base\Browser::device($userAgent):null;
	}


	// setFiles
	// change le tableau de fichier si existant
	protected function setFiles(?array $value=null):self
	{
		if(is_array($value) && !empty($value))
		$this->files = $value;

		return $this;
	}


	// files
	// retourne le tableau fichier
	// par défaut le tableau de retour est parse, pour reformatter si le input est multi-dimensionnel
	public function files(bool $reformat=false):array
	{
		$return = $this->files;

		if($reformat === true && !empty($return))
		$return = Base\Superglobal::filesReformat($return);

		return $return;
	}


	// redirect
	// retourne l'uri de redirection si l'uri de la requête présente des défauts
	// par exemple path unsafe, double slash, slash à la fin ou manque pathLang
	// possibilité de retourner le chemin absolut
	public function redirect(bool $absolute=false):?string
	{
		$return = null;
		$return = Base\Path::redirect($this->path(true),$this->getOptionBase('safe'),$this->getOptionBase('lang'));

		if(is_string($return) && $absolute === true)
		$return = Base\Uri::absolute($return);

		return $return;
	}


	// manageRedirect
	// vérifie la requête et manage les redirections possibles
	// certaines errors vont générer un code http 400 plutôt que 404 (bad request)
	// retourne un tableau avec les clés type, code et location
	// gère externalPost, redirection, unsafe et request
	public function manageRedirect(?Redirection $redirection=null):array
	{
		$return = ['type'=>null,'code'=>null,'location'=>null];
		$isAjax = $this->isAjax();
		$isSafe = $this->isPathSafe();
		$isExternalPost = $this->isExternalPost();
		$schemeHost = $this->schemeHost();
		$redirect = $this->redirect();
		$hasExtension = $this->hasExtension();

		// externalPost
		if($isExternalPost === true)
		{
			$return['type'] = 'externalPost';
			$return['code'] = 400;
		}

		else
		{
			// redirection
			if(empty($return['type']) && !empty($redirection))
			{
				$to = $redirection->get($this);

				if(!empty($to))
				{
					$return['type'] = 'redirection';
					$return['location'] = $to;
				}
			}

			// unsafe
			if(empty($return['type']) && $isSafe === false)
			{
				$return['type'] = 'unsafe';

				if($isAjax === true)
				$return['code'] = 400;

				else
				{
					if($this->absolute() !== $schemeHost && !$hasExtension)
					$return['location'] = $schemeHost;

					else
					$return['code'] = 400;
				}
			}

			// request
			if(empty($return['type']) && !empty($redirect))
			{
				$return['type'] = 'request';

				if($isAjax === true)
				$return['code'] = 400;

				else
				$return['location'] = $redirect;
			}

			if($return['location'] !== null && $return['code'] === null)
			$return['code'] = 302;
		}

		return $return;
	}


	// curl
	// retourne un objet Res avec la resource curl a utilisé pour la requête
	public function curl(?array $option=null):Res
	{
		$return = null;
		$lowOption = ['userAgent'=>$this->userAgent()];
		$highOption = ['uri'=>['encode'=>true],'ssl'=>$this->isSsl(),'port'=>$this->port()];
		$option = Base\Arr::plus($lowOption,$this->option(),$option,$highOption);

		$uri = $this->absolute($option['uri']);
		$post = ($this->isPost())? $this->post():null;
		$header = $this->headers();
		$res = Base\Res::curl($uri,false,$post,$header,$option);

		if(!empty($res))
		$return = Res::newOverload($res);

		else
		static::throw();

		return $return;
	}


	// curlExec
	// lance la requête curl sur la requête courante
	// retourne un tableau avec les headers, la resource et le timestamp
	public function curlExec(?array $option=null):?array
	{
		$return = null;
		$option = Base\Arr::plus($this->option(),$option);

		if(!empty($option['ping']) && is_int($option['ping']))
		{
			$host = $this->host();
			$port = $this->port();

			if(is_string($option['proxyHost']) && is_int($option['proxyPort']))
			static::checkPing($option['proxyHost'],$option['proxyPort'],$option['ping']);

			else
			static::checkPing($host,$port,$option['ping']);
		}

		$curl = $this->curl($option);
		$exec = $curl->curlExec();
		$throw = null;
		$code = null;

		if(empty($exec) || empty($exec['meta']['info']))
		$throw = ['requestFailed'];

		$info = $exec['meta']['info'];

		if(empty($throw) && (!empty($info['errorNo']) || !empty($info['error'])))
		$throw = [$info['errorNo'],$info['error']];

		if(empty($throw) && empty($exec['resource']))
		$throw = ['responseHasNoResource'];

		if(empty($throw) && !Base\Res::isPhpTemp($exec['resource']))
		$throw = ['responseHasInvalidResource'];

		if(empty($throw) && (empty($exec['header']) || !is_array($exec['header'])))
		$throw = ['responseHasNoHeader'];

		$code = Base\Header::code($exec['header']);

		if(empty($throw) && !is_int($code))
		$throw = ['responseHasNoCode'];

		if(!empty($option['responseCode']))
		{
			$responseCode = (array) $option['responseCode'];

			if(!in_array($code,$responseCode,true))
			{
				$strCode = implode(', ',$responseCode);
				$code = ($code === null)? 0:$code;
				static::catchable(null,'responseCodeShouldBe',$strCode,'not',$code);
			}
		}

		if(!empty($throw))
		static::throw(...$throw);

		$return = [];
		$return['header'] = $exec['header'];
		$return['resource'] = $exec['resource'];
		$return['timestamp'] = Base\Date::timestamp();

		return $return;
	}


	// trigger
	// trigger la requête et retourne un objet réponse
	public function trigger(?array $option=null):Response
	{
		return Response::newOverload($this,$option);
	}


	// live
	// créer un objet requête à partir de la requête courante dans base request
	// la requête crée n'agit pas comme référence de la requête courante
	public static function live():self
	{
		return new static(null);
	}


	// default
	// retourne le tableau des défauts pour une nouvelle requête
	public static function default(?array $value=null):array
	{
		$return = [];
		$value = (is_array($value))? Base\Arr::plus(static::$config['default'],$value):static::$config['default'];

		foreach ($value as $key => $value)
		{
			if(is_string($key))
			{
				if(is_string($value))
				$return[$key] = $value;

				elseif(static::classIsCallable($value))
				$return[$key] = $value();
			}
		}

		return $return;
	}


	// checkPing
	// vérifie que l'hôte est joignable sur le port spécifié
	// sinon envoie une exception attrapable
	public static function checkPing(string $host,int $port=80,int $timeout=2):bool
	{
		$return = Base\Network::isOnline($host,$port,$timeout);

		if($return === false)
		static::catchable(null,'hostUnreachable',$host,$port);

		return $return;
	}
}
?>