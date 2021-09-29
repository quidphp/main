<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main;
use Quid\Base;
use Quid\Base\Cli;
use Quid\Base\Html;

// request
// class with methods to manage an HTTP request
class Request extends Map
{
    // trait
    use _inst;
    use Map\_readOnly;


    // config
    protected static array $config = [
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
        'proxyPort'=>null, // port pour proxy, pour curl
        'proxyPassword'=>null, // pour curl
        'followLocation'=>false, // pour curl
        'ssl'=>null, // pour curl
        'port'=>null, // pour curl
        'sslCipher'=>null, // pour curl
        'userAgent'=>null, // pour curl
        'postJson'=>false, // le tableau post est encodé en json, pour curl
        'castData'=>true, // si les datas sont cast
        'ipAllowed'=>[ // paramètre par défaut pour la méhode isIpAllowed
            'whiteList'=>null, // tableau de ip whiteList
            'blackList'=>null, // tableau de ip blackList
            'range'=>true, // comparaison range
            'level'=>null], // comparaison de niveau dans le ip
        'default'=>[ // ces défaut sont appliqués à chaque création d'objet
            'scheme'=>[Base\Request::class,'scheme'], // scheme de request par défaut
            'host'=>[Base\Request::class,'host'], // host de request par défaut
            'port'=>[Base\Request::class,'port'], // port de request par défaut
            'path'=>'/', // path par défaut
            'method'=>'get', // method par défaut
            'lang'=>[Base\Lang::class,'current'], // lang par défaut
            'ip'=>[Base\Server::class,'addr'], // ip du serveur par défaut
            'timestamp'=>[Base\Datetime::class,'now']] // timestamp de date par défaut
    ];


    // required
    protected static array $required = ['id','scheme','host','path','method']; // propriété requise, ne peuvent pas être null


    // dynamique
    protected ?string $id = null; // id unique de la requête
    protected ?string $scheme = null; // scheme de la requête
    protected ?string $user = null; // user de la requête
    protected ?string $pass = null; // pass de la requête
    protected ?string $host = null; // host de la requête
    protected ?int $port = null; // port de la requête
    protected ?string $path = null; // path relative de la requête
    protected ?string $query = null; // query de la requête, en string
    protected ?string $fragment = null; // fragment de la requête
    protected ?string $method = null; // method de la requête
    protected ?string $ip = null; // ip de la requête
    protected array $headers = []; // headers de la requête
    protected ?int $timestamp = null; // timestamp de la requête
    protected ?string $lang = null; // lang de la requête, peut provenir de path
    protected array $files = []; // contient les données de fichier
    protected bool $cli = false; // défini si c'est une requête du cli
    protected bool $live = false; // défini si la requête représente la live
    protected ?array $log = null; // permet de conserver des datas à logger


    // construct
    // construit l'objet requête
    // value peut être une string (uri) ou un tableau
    // si value est null, c'est la requête courante avec les options de base/request
    public function __construct($value=null,?array $attr=null)
    {
        $this->makeAttr($attr);

        $live = false;
        if($value === null)
        {
            $live = true;
            $value = Base\Request::export(true,true);
        }

        if(is_string($value))
        $this->setUri($value);

        elseif(is_array($value))
        $this->change($value);

        if(empty($this->id))
        $this->setId();

        $this->setDefault(true);

        $this->setLive($live);
    }


    // invoke
    // retourne une des propriétés de l'objet via une des méthodes get
    final public function __invoke(...$args)
    {
        $return = null;

        if(!empty($args))
        {
            $key = current($args);

            if(is_string($key) && $this->hasProperty($key) && $this->hasMethod($key))
            $return = $this->$key();

            else
            static::throw('invalid',$key);
        }

        return $return;
    }


    // toString
    // retourne la méthode output cast en string
    final public function __toString():string
    {
        return Base\Str::cast($this->output());
    }


    // onSetInst
    // méthode appeler après setInst
    final protected function onSetInst():void
    {
        $this->readOnly(true);
    }


    // onUnsetInst
    // méthode appeler après unsetInst
    final protected function onUnsetInst():void
    {
        $this->readOnly(false);
    }


    // cast
    // retourne la valeur cast, la méthode output
    final public function _cast():string
    {
        return $this->output();
    }


    // toArray
    // retourne tout l'objet sous forme de tableau
    final public function toArray():array
    {
        return get_object_vars($this);
    }


    // jsonSerialize
    // json serialize la requête avec le id
    // retourne save info
    final public function jsonSerialize():array
    {
        $this->checkAllowed('jsonSerialize');
        return $this->safeInfo(true);
    }


    // isLive
    // retourne vrai si la requête est live
    final public function isLive():bool
    {
        return $this->live;
    }


    // setLive
    // genre la valeur à la propriété live
    final protected function setLive(bool $value):void
    {
        $this->live = $value;
    }


    // isCli
    // retourne vrai si la requête est du cli
    final public function isCli():bool
    {
        return $this->cli;
    }


    // setCli
    // détermine si la requête provient du cli
    // value doit être bool
    final public function setCli(bool $value):self
    {
        $this->cli = $value;

        return $this;
    }


    // getLogData
    // retourne les data pour le log
    final public function getLogData():?array
    {
        return $this->log;
    }


    // setLog
    // permet d'attributer des datas au log
    final public function setLogData(?array $value):self
    {
        $this->log = $value;

        return $this;
    }


    // getAttrBase
    // permet d'obtenir une option merge avec les config dans base/request
    final public function getAttrBase(string $key)
    {
        $return = $this->getAttr($key);
        $base = Base\Request::getConfig($key);

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
    final public function setDefault(bool $fill=true):self
    {
        $default = $this->default();

        if($fill === true)
        {
            $change = [];
            foreach ($default as $key => $value)
            {
                if($this->$key === null)
                $change[$key] = $value;
            }
        }

        else
        $change = $default;

        if(!empty($change))
        $this->change($change);

        return $this->checkRequired();
    }


    // str
    // envoie info à http str qui retourne une string pour représenter la requête
    final public function str(?array $option=null):string
    {
        return Base\Http::str($this->info(),$option);
    }


    // uri
    // construit l'uri à partir du tableau parse
    // n'est pas encodé ou décodé, plus rapide que les autres méthodes
    // absolut est true si le host de la requête n'est pas celui courant
    final public function uri(?bool $absolute=null):string
    {
        if($absolute === null)
        $absolute = ($this->isSchemeHostCurrent())? false:true;

        if($absolute === true)
        $parse = $this->parse();
        else
        $parse = ['path'=>$this->path(),'query'=>$this->query(),'fragment'=>$this->fragment()];

        return Base\Uri::build($parse);
    }


    // output
    // retourne l'uri, peut être relatif ou absolut dépendamment des options uri
    final public function output(?array $option=null):?string
    {
        return Base\Uri::output($this->uri(true),Base\Arr::plus($this->getAttr('uri'),$option));
    }


    // relative
    // retourne l'uri relative de la requête
    final public function relative(?array $option=null):?string
    {
        return Base\Uri::relative($this->uri(false),Base\Arr::plus($this->getAttr('uri'),$option));
    }


    // relativeExists
    // retourne l'uri relative de la requête, base uri va vérifier qu'elle existe
    final public function relativeExists(?array $option=null)
    {
        return Base\Uri::relative($this->uri(false),Base\Arr::plus($this->getAttr('uri'),$option,['exists'=>true]));
    }


    // absolute
    // retourne l'uri absolue de la requête
    final public function absolute(?array $option=null):?string
    {
        return Base\Uri::absolute($this->uri(true),null,Base\Arr::plus($this->getAttr('uri'),$option));
    }


    // setUri
    // change l'uri de la requête
    // plusieurs paramètres peuvent être pris de l'uri, pas seulement le path
    // par exemple host, user, pass, port, query et fragment
    // par l'option par défaut, l'uri est décodé
    final public function setUri(string $value,?bool $decode=null):self
    {
        $parse = Base\Uri::parse($value,$decode ?? $this->getAttr('decode'));

        if(!empty($parse))
        {
            $change = Base\Arr::cleanNull($parse);
            $this->change($change);
        }

        return $this;
    }


    // setAbsolute
    // alias de setUri
    final public function setAbsolute(string $value,?bool $decode=null):self
    {
        return $this->setUri($value,$decode);
    }


    // info
    // retourne un tableau complet à partir de la requête
    // possible d'exporter le id
    final public function info(bool $id=false):array
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
        $return['files'] = $this->filesArray();
        $return['headers'] = $this->headers();
        $return['userAgent'] = $this->userAgent();
        $return['referer'] = $this->referer();
        $return['lang'] = $this->lang();
        $return['safe'] = $this->isPathSafe();
        $return['cachable'] = $this->isCachable();
        $return['redirectable'] = $this->isRedirectable();
        $return['cli'] = $this->isCli();

        if($id === true)
        $return['id'] = $this->id();

        return $return;
    }


    // safeInfo
    // exporte les informations de la requête, utiliser par Tablelog
    // similaire à info pour mais post et headers incluent seulement les clés et true comme valeur
    // possible d'exporter le id
    final public function safeInfo(bool $id=false):array
    {
        $return = $this->parse();

        $return['absolute'] = $this->absolute();
        $return['method'] = $this->method();
        $return['ajax'] = $this->isAjax();
        $return['ssl'] = $this->isSsl();
        $return['timestamp'] = $this->timestamp();
        $return['ip'] = $this->ip();
        $return['post'] = $this->postExport();
        $return['files'] = Base\Arr::combine(array_keys($this->filesArray()),true);
        $return['headers'] = $this->headers();
        $return['lang'] = $this->lang();
        $return['cli'] = $this->isCli();

        if($id === true)
        $return['id'] = $this->id();

        return $return;
    }


    // export
    // exporte les informations liés à la requête courante
    // possible d'exporter le id
    final public function export(bool $id=false):array
    {
        $return = $this->parse();
        $return['method'] = $this->method();
        $return['timestamp'] = $this->timestamp();
        $return['ip'] = $this->ip();
        $return['post'] = $this->post();
        $return['files'] = $this->filesArray();
        $return['headers'] = $this->headers();
        $return['lang'] = $this->lang();
        $return['cli'] = $this->isCli();

        if($id === true)
        $return['id'] = $this->id();

        return $return;
    }


    // parse
    // retourne le tableau parse de l'objet
    final public function parse():array
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
    final public function change(array $value):self
    {
        foreach (Base\Request::prepareChangeArray($value) as $key => $value)
        {
            if(is_string($key))
            {
                $method = 'set'.ucfirst($key);

                if($this->hasMethod($method))
                $this->$method($value);
            }
        }

        return $this;
    }


    // property
    // fait un changement sur une propriété
    // si requête est live et liveBase est true, renvoie à base/request pour faire le changement la aussi
    // fait le checkReadOnly
    final protected function property(string $key,$value,bool $liveBase=true):self
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
    final public function isSsl():bool
    {
        return Base\Http::ssl($this->scheme);
    }


    // isAjax
    // retourne vrai si la requête est ajax
    final public function isAjax():bool
    {
        return $this->header('X-Requested-With') === 'XMLHttpRequest';
    }


    // isGet
    // retourne vrai si la requête est get
    final public function isGet():bool
    {
        return $this->method() === 'get';
    }


    // isPost
    // retourne vrai si la requête est post
    final public function isPost():bool
    {
        return $this->method() === 'post';
    }


    // isPostWithoutData
    // retourne vrai si la requête est post mais qu'il n'y a pas de données post
    // ceci peut arriver lors du chargement d'un fichier plus lourd que php ini
    final public function isPostWithoutData():bool
    {
        return $this->isPost() && empty($this->post());
    }


    // isRefererInternal
    // retourne vrai si le referrer est interne (donc même host que le courant)
    // possible de fournir un tableau d'autres hosts considérés comme internal
    final public function isRefererInternal($hosts=null):bool
    {
        return !empty($this->referer(true,$hosts));
    }


    // isInternalPost
    // retourne vrai si la requête semble être un post avec un referer provenant du même domaine
    // possible de fournir un tableau d'autres hosts considérés comme internal
    final public function isInternalPost($hosts=null):bool
    {
        return $this->isPost() && $this->isRefererInternal($hosts);
    }


    // isExternalPost
    // retourne vrai si la requête semble être un post avec un referer provenant d'un autre domaine
    // possible de fournir un tableau d'autres hosts considérés comme internal
    final public function isExternalPost($hosts=null):bool
    {
        return $this->isPost() && !$this->isRefererInternal($hosts);
    }


    // isStandard
    // retourne vrai si la requête est de méthode get et pas ajax et pas cli
    final public function isStandard():bool
    {
        return $this->isGet() && !$this->isAjax() && !$this->isCli();
    }


    // isPathEmpty
    // retourne vrai si le chemin de la requête est vide (ou seulement /)
    final public function isPathEmpty():bool
    {
        return $this->path(true) === '';
    }


    // isPathMatchEmpty
    // retourne vrai si le chemin match de la requête est vide (ou seulement /)
    final public function isPathMatchEmpty():bool
    {
        return $this->pathMatch() === '';
    }


    // isSelectedUri
    // retourne vrai si la requête est sélectionné dans base attr
    final public function isSelectedUri():bool
    {
        return Base\Attr::isSelectedUri($this->uri());
    }


    // isCachable
    // retourne vrai si la requête est cachable
    // ce doit être une requête de méthode get, sans post et avec chemin sécuritaire
    final public function isCachable():bool
    {
        return $this->isGet() && !$this->hasPost() && $this->isPathSafe();
    }


    // isRedirectable
    // retourne vrai si la requête est redirectable
    // ce doit être une requête de méthode get, sans post et avec chemin sécuritaire
    final public function isRedirectable():bool
    {
        return $this->isGet() && !$this->hasPost() && $this->isPathSafe();
    }


    // isFailedFileUpload
    // retourne vrai si la requête semble être un envoie de fichier raté
    // doit être live
    final public function isFailedFileUpload():bool
    {
        return $this->isLive() && $this->isPostWithoutData() && Base\Superglobal::hasServerLengthWithoutPost();
    }


    // hasExtension
    // retourne vrai si la requête a une extension
    final public function hasExtension():bool
    {
        return Base\Path::hasExtension($this->path());
    }


    // hasQuery
    // retourne vrai si la requête a un query
    final public function hasQuery():bool
    {
        return !empty($this->query);
    }


    // hasLang
    // retourne vrai si la requête a une lang
    final public function hasLang():bool
    {
        return !empty($this->lang);
    }


    // hasPost
    // retourne vrai si la requête courante contient des données post
    final public function hasPost():bool
    {
        return !empty($this->post());
    }


    // hasData
    // retourne vrai si la requête courante contient des données get ou post
    final public function hasData():bool
    {
        return $this->hasQuery() || $this->hasPost();
    }


    // hasValidGenuine
    // retourne vrai si post contient la clé genuine et le contenu est vide
    // genuine 2 est un champ ajouté sur le front-end
    final public function hasValidGenuine(bool $two=true):bool
    {
        $return = false;
        $post = $this->post();
        $genuine = Html::getGenuineName();
        $genuine2 = Html::getGenuineName(2);

        if(!empty($genuine) && !empty($post) && array_key_exists($genuine,$post) && empty($post[$genuine]))
        $return = ($two === false || (array_key_exists($genuine2,$post) && !empty($post[$genuine2])));

        return $return;
    }


    // hasUser
    // retourne vrai si la requête courante contient un user
    final public function hasUser():bool
    {
        return is_string($this->user());
    }


    // hasPass
    // retourne vrai si la requête courante contient un pass
    final public function hasPass():bool
    {
        return is_string($this->pass());
    }


    // hasFragment
    // retourne vrai si la requête courante contient un fragment
    final public function hasFragment():bool
    {
        return is_string($this->fragment());
    }


    // hasIp
    // retourne vrai si la requête courante contient un ip
    final public function hasIp():bool
    {
        return !empty($this->ip());
    }


    // hasUserAgent
    // retourne vrai si la requête courante contient un userAgent
    final public function hasUserAgent():bool
    {
        return !empty($this->userAgent());
    }


    // hasHeaders
    // retourne vrai si le tableau headers n'est pas vide
    final public function hasHeaders():bool
    {
        return !empty($this->headers());
    }


    // isHeader
    // retourne vrai si la ou les clés headers existent
    final public function isHeader(...$keys):bool
    {
        return Base\Header::exists($keys,$this->headers());
    }


    // isBot
    // retourne vrai si le userAgent est un bot
    final public function isBot():bool
    {
        return Base\Browser::isBot($this->userAgent());
    }


    // isInternal
    // retourne vrai si la requête est interne, même host que la requête courante
    final public function isInternal():bool
    {
        return Base\Uri::isInternal($this->uri());
    }


    // isExternal
    // retourne vrai si la requête est externe, host différent que requête courante
    final public function isExternal():bool
    {
        return Base\Uri::isExternal($this->uri());
    }


    // isScheme
    // retourne vrai si la requête a un scheme du type spécifié
    final public function isScheme($value):bool
    {
        return Base\Uri::isScheme($value,$this->uri());
    }


    // isHost
    // retourne vrai si la requête a l'hôte spécifié
    final public function isHost($value):bool
    {
        return Base\Uri::isHost($value,$this->uri());
    }


    // isSchemeHost
    // retourne vrai si la requête a le schemeHost spécifié
    final public function isSchemeHost($value):bool
    {
        return Base\Uri::isSchemeHost($value,$this->uri());
    }


    // isExtension
    // retourne vrai si la requête à une extension du type
    // possibilité de mettre une ou plusieurs target
    final public function isExtension(...$values):bool
    {
        return Base\Uri::isExtension($values,$this->uri());
    }


    // isQuery
    // retourne vrai si l'uri a des query en argument
    // possibilité de mettre une ou plusieurs valeurs
    final public function isQuery(...$values):bool
    {
        return Base\Arr::keysExists($values,$this->queryArray());
    }


    // isLang
    // retourne vrai si la requête a la langue spécifié
    final public function isLang($value):bool
    {
        return Base\Path::isLang($value,$this->path());
    }


    // isIp
    // retourne vrai si le ip est celui fourni
    final public function isIp($value):bool
    {
        return is_string($value) && $value === $this->ip();
    }


    // isIpLocal
    // retourne vrai si le ip est local
    final public function isIpLocal():bool
    {
        return $this->hasIp() && Base\Ip::isLocal($this->ip());
    }


    // isIpAllowed
    // retourne vrai si le ip est permis en fonction du whitelist et blacklist gardé dans les config de la classe
    final public function isIpAllowed():bool
    {
        return $this->hasIp() && Base\Ip::allowed($this->ip(),$this->getAttr('ipAllowed'));
    }


    // isPathSafe
    // retourne vrai si le path est considéré comme safe
    final public function isPathSafe():bool
    {
        return Base\Path::isSafe($this->path(),$this->getAttrBase('safe'));
    }


    // isPathArgument
    // retourne vrai si le chemin est un argument (commence par - )
    final public function isPathArgument():bool
    {
        return Base\Path::isArgument($this->path());
    }


    // isPathArgumentNotCli
    // retourne vrai si le chemin est un argument (commence par - ) mais que la requête n'est pas cli
    final public function isPathArgumentNotCli():bool
    {
        return $this->isPathArgument() && !$this->isCli();
    }


    // hasFiles
    // retourne vrai si la requête contient des fichiers
    final public function hasFiles():bool
    {
        return !empty($this->files);
    }


    // checkPathSafe
    // envoie une exception si le chemin n'est pas safe
    final public function checkPathSafe():self
    {
        if(!$this->isPathSafe())
        static::throw();

        return $this;
    }


    // checkRequired
    // lance une exception si une des propriétés requises est null
    final public function checkRequired():self
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
    final public function checkAfter():self
    {
        if($this->getAttr('castData') === true)
        $this->data = Base\Arrs::cast($this->data);

        return $this;
    }


    // setId
    // change le id unique de la requête
    // si value est null, génère un id unique en utilisant la config id length
    final public function setId(?string $value=null):self
    {
        if(empty($value))
        $value = Base\Str::random($this->getAttrBase('idLength'));

        $this->property('id',$value);

        return $this;
    }


    // id
    // retourne le id unique de la requête
    final public function id():string
    {
        return $this->id;
    }


    // scheme
    // retourne le scheme courant de la requête
    final public function scheme():?string
    {
        return $this->scheme;
    }


    // setScheme
    // change le scheme courant de la requête
    // change aussi le port
    // value ne peut pas être null
    final public function setScheme(string $value):self
    {
        if(!Base\Uri::isSchemeValid($value))
        static::throw('unsupportedScheme');

        $this->property('scheme',$value);

        $port = Base\Http::port($value);
        if(is_int($port) && $port !== $this->port)
        $this->setPort($port);

        return $this;
    }


    // user
    // retourne le user de la requête
    final public function user():?string
    {
        return $this->user;
    }


    // setUser
    // change le user de la requête
    // value peut être null
    final public function setUser(?string $value):self
    {
        return $this->property('user',$value);
    }


    // pass
    // retourne le pass de la requête
    final public function pass():?string
    {
        return $this->pass;
    }


    // setPass
    // change le pass de la requête
    // value peut être null
    final public function setPass(?string $value):self
    {
        return $this->property('pass',$value);
    }


    // host
    // retourne le host de la requête
    final public function host():?string
    {
        return $this->host;
    }


    // setHost
    // change le host de la requête
    // value ne peut pas être null
    final public function setHost(string $value):self
    {
        if(!Base\Http::isHost($value))
        static::throw();

        return $this->property('host',$value);
    }


    // isSchemeHostCurrent
    // retourne vrai si le scheme host est celui de la requête courante
    // passe dans base/request
    final public function isSchemeHostCurrent():bool
    {
        return Base\Request::isSchemeHost($this->schemeHost());
    }


    // port
    // retourne le port de la requête
    final public function port():?int
    {
        return $this->port;
    }


    // setPort
    // change le port de la requête
    // change aussi le scheme
    // value peut être null
    final public function setPort(?int $value):self
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
    final public function path(bool $stripStart=false):?string
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
    final public function setPath(string $value):self
    {
        $this->property('path',$value);

        $lang = Base\Path::lang($value,$this->getAttrBase('lang'));
        if(!empty($lang))
        $this->setLang($lang);

        return $this;
    }


    // pathStripStart
    // retourne le path de la requête sans le séparateur au début
    final public function pathStripStart():?string
    {
        return $this->path(true);
    }


    // pathinfo
    // retourne le tableau pathinfo de la requête
    // peut aussi retourner une seule variable pathinfo
    final public function pathinfo(?int $key=null)
    {
        return Base\Path::info($this->uri(),$key);
    }


    // changePathinfo
    // change un ou plusieurs éléments du pathinfo de la requête
    final public function changePathinfo(array $change):self
    {
        return $this->setPath(Base\Path::change($change,$this->path()));
    }


    // keepPathinfo
    // garde un ou plusieurs éléments du pathinfo de la requête
    final public function keepPathinfo(array $change):self
    {
        return $this->setPath(Base\Path::keep($change,$this->path()));
    }


    // removePathinfo
    // enlève un ou plusieurs éléments du pathinfo de la requête
    final public function removePathinfo(array $change):self
    {
        return $this->setPath(Base\Path::remove($change,$this->path()));
    }


    // dirname
    // retourne le dirname du path de la requête
    final public function dirname():?string
    {
        return Base\Path::dirname($this->path());
    }


    // addDirname
    // ajoute un dirname après le dirname du path de la requête
    final public function addDirname(string $change):self
    {
        return $this->setPath(Base\Path::addDirname($change,$this->path()));
    }


    // changeDirname
    // change le dirname du path de la requête
    final public function changeDirname(string $change):self
    {
        return $this->setPath(Base\Path::changeDirname($change,$this->path()));
    }


    // removeDirname
    // enlève un dirname au path de la requête
    final public function removeDirname():self
    {
        return $this->setPath(Base\Path::removeDirname($this->path()));
    }


    // basename
    // retourne le basename du path de la requête
    final public function basename():?string
    {
        return Base\Path::basename($this->path());
    }


    // addBasename
    // ajoute un basename après le dirname du path de la requête
    final public function addBasename(string $change):self
    {
        return $this->setPath(Base\Path::addBasename($change,$this->path()));
    }


    // changeBasename
    // change le basename du path de la requête
    final public function changeBasename(string $change):self
    {
        return $this->setPath(Base\Path::changeBasename($change,$this->path()));
    }


    // removeBasename
    // enlève le basename du path de la requête
    final public function removeBasename():self
    {
        return $this->setPath(Base\Path::removeBasename($this->path()));
    }


    // filename
    // retourne le filename du path de la requête
    final public function filename():?string
    {
        return Base\Path::filename($this->path());
    }


    // addFilename
    // ajoute un filename après le dirname du path de la requête
    final public function addFilename(string $change):self
    {
        return $this->setPath(Base\Path::addFilename($change,$this->path()));
    }


    // changeFilename
    // change le filename du path de la requête
    final public function changeFilename(string $change):self
    {
        return $this->setPath(Base\Path::changeFilename($change,$this->path()));
    }


    // removeFilename
    // enlève un filename du path de la requête
    final public function removeFilename():self
    {
        return $this->setPath(Base\Path::removeFilename($this->path()));
    }


    // extension
    // retourne l'extension du path de la requête
    final public function extension():?string
    {
        return Base\Path::extension($this->path());
    }


    // addExtension
    // ajoute l'extension après le dirname du path de la requête
    final public function addExtension(string $change):self
    {
        return $this->setPath(Base\Path::addExtension($change,$this->path()));
    }


    // changeExtension
    // change l'extension du path de la requête
    final public function changeExtension(string $change):self
    {
        return $this->setPath(Base\Path::changeExtension($change,$this->path()));
    }


    // removeExtension
    // enlève une extension au path de la requête
    final public function removeExtension():self
    {
        return $this->setPath(Base\Path::removeExtension($this->path()));
    }


    // mime
    // retourne le mimetype du path de la requête à partir de son extension
    // ne vérifie pas l'existence du fichier
    final public function mime():?string
    {
        return Base\Path::mime($this->path());
    }


    // addLang
    // ajoute un code de langue au path de la requête
    // ajoute même si le code existe déjà
    // le path sera retourné vide si le code langue est invalide
    final public function addLang(string $change):self
    {
        return $this->setPath(Base\Path::addLang($change,$this->path()));
    }


    // changeLang
    // ajoute ou remplace un code de langue au path de la requête
    // le path sera retourné vide si le code langue est invalide
    final public function changeLang(string $change):self
    {
        return $this->setPath(Base\Path::changeLang($change,$this->path()));
    }


    // removeLang
    // enlève un code de langue au path de la requête
    // retourne le chemin dans tous les cas
    final public function removeLang():self
    {
        return $this->setPath(Base\Path::removeLang($this->path()));
    }


    // pathPrepend
    // prepend un path derrière le path de la requête
    final public function pathPrepend(string $value):self
    {
        return $this->setPath(Base\Path::prepend($this->path(true),$value));
    }


    // pathAppend
    // append un path devant le path de la requête
    final public function pathAppend(string $value):self
    {
        return $this->setPath(Base\Path::append($this->path(true),$value));
    }


    // pathExplode
    // explode le path de la requête
    final public function pathExplode():array
    {
        return Base\Path::arr($this->path(true));
    }


    // pathGet
    // retourne un index du path de la requête
    final public function pathGet(int $index):?string
    {
        return Base\Path::get($index,$this->path(true));
    }


    // pathCount
    // count le nombre de niveau dans le path de la requête
    final public function pathCount():int
    {
        return Base\Path::count($this->path(true));
    }


    // pathSlice
    // tranche des slices du path de la requête en utilisant offset et length
    final public function pathSlice(int $offset,?int $length):array
    {
        return Base\Path::slice($offset,$length,$this->path(true));
    }


    // pathSplice
    // efface et remplace des slices du path de la requête en utilisant offset et length
    final public function pathSplice(int $offset,?int $length,$replace=null):self
    {
        return $this->setPath(Base\Path::splice($offset,$length,$this->path(true),$replace));
    }


    // pathInsert
    // ajoute un ou plusieurs éléments dans le path sans ne rien effacer
    final public function pathInsert(int $offset,$replace):self
    {
        return $this->setPath(Base\Path::insert($offset,$replace,$this->path(true)));
    }


    // pathMatch
    // retourne le chemin sans le code de langue et sans le wrap du début
    final public function pathMatch():string
    {
        return Base\Path::match($this->path(),$this->getAttrBase('lang'));
    }


    // query
    // retourne la query string de la requête
    final public function query():?string
    {
        return $this->query;
    }


    // setQuery
    // change la query string de la requête
    // accepte un tableau ou string en argument
    // value peut être null
    final public function setQuery($value):self
    {
        if(is_array($value))
        $value = Base\Uri::buildQuery($value,false);

        if(!is_string($value) && $value !== null)
        static::throw();

        return $this->property('query',$value);
    }


    // queryArray
    // retourne la query string de la requête sous forme de tableau
    final public function queryArray():array
    {
        return (is_string($this->query))? Base\Uri::parseQuery($this->query):[];
    }


    // getQuery
    // retourne la valeur d'une clé get query
    final public function getQuery($key)
    {
        return Base\Arr::get($key,$this->queryArray());
    }


    // getsQuery
    // retournes plusieurs valeurs dans query
    final public function getsQuery(...$keys):array
    {
        return Base\Arr::gets($keys,$this->queryArray());
    }


    // addQuery
    // insert ou update une valeur dans query
    final public function addQuery($key,$value):self
    {
        return $this->setQuery(Base\Uri::buildQuery(Base\Arr::set($key,$value,$this->queryArray())));
    }


    // setsQuery
    // insert ou update une ou plusieurs valeurs dans query
    final public function setsQuery(array $values):self
    {
        return $this->setQuery(Base\Uri::buildQuery(Base\Arr::sets($values,$this->queryArray())));
    }


    // unsetQuery
    // enlève une ou plusieurs clés dans query
    final public function unsetQuery(...$keys):self
    {
        return $this->setQuery(Base\Uri::buildQuery(Base\Arr::unsets($keys,$this->queryArray())));
    }


    // setArgv
    // permet de lier des query à la requête à partir d'un tableau d'options de cli
    final public function setArgv(array $values):self
    {
        $query = Cli::parseOpt(...array_values($values));

        if(!empty($query))
        $this->setQuery($query);

        return $this;
    }


    // fragment
    // retourne le fragment de la requête
    final public function fragment():?string
    {
        return $this->fragment;
    }


    // setFragment
    // change le fragment de la requête
    // value peut être null
    final public function setFragment(?string $value):self
    {
        return $this->property('fragment',$value);
    }


    // lang
    // retourne la langue de la requête
    // la langue est automatiquement considéré à partir du path
    final public function lang():?string
    {
        return $this->lang;
    }


    // setLang
    // change la langue de la requête, créer aussi le header accept-language
    // value peut être null
    final public function setLang(?string $value,bool $header=true):self
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
    final public function langHeader()
    {
        return $this->header('Accept-Language');
    }


    // setLangHeader
    // change le accept-language de la requête dans les headers
    // value peut être null
    final public function setLangHeader(?string $value):self
    {
        $this->setHeader('Accept-Language',$value);

        return $this;
    }


    // schemeHost
    // retourne le scheme et host de l'uri de la requête
    final public function schemeHost():string
    {
        return Base\Uri::schemeHost($this->uri(true));
    }


    // schemeHostPath
    // retourne le scheme, domaine et path de l'uri de la requête
    final public function schemeHostPath():string
    {
        return Base\Uri::schemeHostPath($this->uri(true));
    }


    // hostPath
    // retourne le domaine et path de l'uri de la requête
    final public function hostPath():string
    {
        return Base\Uri::hostPath($this->uri(true));
    }


    // pathQuery
    // retourne le path et la query de la requête
    final public function pathQuery():string
    {
        return Base\Uri::pathQuery($this->uri(true));
    }


    // method
    // retourne la méthode de la requête
    final public function method():?string
    {
        return $this->method;
    }


    // setMethod
    // change la méthode de la requête
    // value ne peut pas être null
    final public function setMethod(string $value):self
    {
        if(!Base\Http::isMethod($value))
        static::throw('invalidMethod',$value);

        return $this->property('method',strtolower($value));
    }


    // setAjax
    // change la valeur ajax de la requête dans les headers
    // value doit être bool
    final public function setAjax(bool $value):self
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
    final public function setSsl(bool $value):self
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
    final public function post(bool $safeKey=false,bool $stripTags=false,?string $notStart=null,bool $includeFiles=false):array
    {
        $files = ($includeFiles === true)? $this->filesArray():null;
        return Base\Superglobal::postReformat($this->arr(),$safeKey,$stripTags,$notStart,$includeFiles,$files);
    }


    // postExport
    // retourne le tableau de post pour une exporation de la requête
    final public function postExport():array
    {
        $return = [];
        $post = $this->post();

        foreach ($post as $key => $value)
        {
            if(Base\Validate::isCol($key))
            $value = true;

            $return[$key] = $value;
        }

        return $return;
    }


    // postJson
    // retourne le tableau post de la requête sous forme de json
    final public function postJson(bool $onlyCol=false,bool $stripTags=false,bool $includeFiles=false):string
    {
        return Base\Json::encode($this->post($onlyCol,$stripTags,null,$includeFiles));
    }


    // postQuery
    // retourne le tableau post de la requête sous forme de query
    final public function postQuery(bool $onlyCol=false,bool $stripTags=false,bool $encode=true):?string
    {
        return Base\Uri::buildQuery($this->post($onlyCol,$stripTags),$encode);
    }


    // postTimestamp
    // retourne le timestamp du post, tel que stocké dans le champ caché du formulaire
    final public function postTimestamp():?int
    {
        $return = null;
        $post = $this->post();
        $timestamp = Html::getTimestampName();

        if(!empty($timestamp) && !empty($post) && array_key_exists($timestamp,$post))
        $return = $post[$timestamp];

        return $return;
    }


    // csrf
    // retourne la chaîne csrf de la requête
    final public function csrf():?string
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
    final public function captcha():?string
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
    final public function setPost(array $value):self
    {
        if(!empty($value))
        $this->setMethod('post');

        $return = $this->overwrite($value);

        return $return;
    }


    // ip
    // retourne le ip de la requête
    final public function ip():?string
    {
        return $this->ip;
    }


    // setIp
    // change le ip de la requête
    // value peut être null
    final public function setIp(?string $value):self
    {
        if($value !== null && !Base\Ip::is($value))
        static::throw();

        return $this->property('ip',$value);
    }


    // userAgent
    // retourne le userAgent de la requête
    final public function userAgent()
    {
        return $this->header('User-Agent');
    }


    // setUserAgent
    // change le userAgent de la requête dans les headers
    // value peut être null
    final public function setUserAgent(?string $value):self
    {
        $this->setHeader('User-Agent',$value);

        return $this;
    }


    // referer
    // retourne l'uri référent à la requête
    // possible de retourner seulement si le referer est interne (et possible de spécifier un tableau d'host considéré comme interne)
    final public function referer(bool $internal=false,$hosts=null):?string
    {
        $return = null;
        $referer = $this->header('Referer');

        if(is_string($referer) && !empty($referer))
        {
            if($internal === false || Base\Uri::host($referer) === 'localhost' || Base\Uri::isInternal($referer,$hosts))
            $return = $referer;
        }

        return $return;
    }


    // setReferer
    // change le setReferer de la requête dans les headers
    // value peut être null
    final public function setReferer(?string $value):self
    {
        $this->setHeader('Referer',$value);

        return $this;
    }


    // timestamp
    // retourne le timestamp de la requête
    final public function timestamp():?int
    {
        return $this->timestamp;
    }


    // setTimestamp
    // change le timestamp de la requête
    // value peut être null
    final public function setTimestamp(?int $value):self
    {
        return $this->property('timestamp',$value);
    }


    // headers
    // retourne les headers de la requête
    final public function headers():array
    {
        return $this->headers;
    }


    // header
    // retourne un header de la requête
    final public function header(string $key)
    {
        return Base\Header::get($key,$this->headers());
    }


    // setHeaders
    // remplace les headers
    // value doit être un tableau
    final public function setHeaders(array $values):self
    {
        $this->property('headers',[]);
        $this->addHeaders($values);

        return $this;
    }


    // addHeaders
    // ajoute les headers à ceux existant
    final public function addHeaders(array $values):self
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
    final public function setHeader(string $key,$value):self
    {
        return $this->property('headers',Base\Header::set($key,$value,$this->headers()));
    }


    // unsetHeader
    // enlève un ou plusieurs headers
    final public function unsetHeader(...$keys):self
    {
        return $this->property('headers',Base\Header::unsets($keys,$this->headers()));
    }


    // fingerprint
    // retourne le fingerprint des headers
    final public function fingerprint(array $keys):?string
    {
        return Base\Header::fingerprint($this->headers(),$keys);
    }


    // setFiles
    // change les fichiers liés à la requête si existant
    final public function setFiles(?array $array=null):self
    {
        $value = [];
        if(is_array($array))
        {
            $array = Files::uploadArrayReformat($array,true);
            $value = Base\Superglobal::filesReformat($array);
        }

        $this->files = $value;

        return $this;
    }


    // filesArray
    // retourne le tableau fichier
    final public function filesArray():array
    {
        return $this->files;
    }


    // files
    // retourne un objet files pour le contenu d'un champ
    // retourne null si le champ n'existe pas
    final public function files($key):?Files
    {
        $return = null;
        $array = $this->filesArray();

        if(Base\Arr::isKey($key) && array_key_exists($key,$array) && !empty($array[$key]))
        {
            $return = Files::newOverload();

            foreach ($array[$key] as $k => $v)
            {
                if(Base\File::isUploadNotEmpty($v))
                $return->set($k,$v);
            }
        }

        return $return;
    }


    // file
    // retourne un fichier d'un champ
    // peut retourner null
    final public function file($key,int $index=0):?File
    {
        $return = null;
        $files = $this->files($key);

        if(!empty($files))
        $return = $files->get($index);

        return $return;
    }


    // redirect
    // retourne l'uri de redirection si l'uri de la requête présente des défauts
    // par exemple path unsafe, double slash, slash à la fin ou manque pathLang
    // possibilité de retourner le chemin absolut
    final public function redirect(bool $absolute=false):?string
    {
        $return = null;
        $return = Base\Path::redirect($this->path(true),$this->getAttrBase('safe'),$this->getAttrBase('lang'));

        if(is_string($return) && $absolute === true)
        $return = Base\Uri::absolute($return);

        return $return;
    }


    // ping
    // permet de faire un ping sur le host et port de la requête
    final public function ping(int $timeout=2,?string $proxyHost=null,?int $proxyPort=null):bool
    {
        $host = $proxyHost ?? $this->host();
        $port = $proxyPort ?? $this->port();
        return Base\Network::isOnline($host,$port,$timeout);
    }


    // checkPing
    // fait un ping et envoie une exception si impossible à rejoindre
    final public function checkPing(int $timeout=2,?string $proxyHost=null,?int $proxyPort=null):bool
    {
        $return = $this->ping($timeout,$proxyHost,$proxyPort);
        $host = $proxyHost ?? $this->host();
        $port = $proxyPort ?? $this->port();

        if($return === false)
        static::catchable(null,'hostUnreachable',$host,$port);

        return $return;
    }


    // curl
    // retourne la resource ou l'objet curl handle
    final public function curl(?array $option=null)
    {
        $lowOption = ['userAgent'=>$this->userAgent()];
        $highOption = ['uri'=>['encode'=>true],'ssl'=>$this->isSsl(),'port'=>$this->port(),'method'=>$this->method()];
        $option = Base\Arr::plus($lowOption,$this->attr(),$option,$highOption);

        $uri = $this->absolute($option['uri']);
        $post = ($this->isPost())? $this->post():null;
        $header = $this->headers();
        $return = Base\Curl::make($uri,false,$post,$header,$option) ?? static::throw();

        return $return;
    }


    // curlExec
    // lance la requête curl sur la requête courante
    // retourne un tableau avec les headers, la resource et le timestamp
    final public function curlExec(?array $option=null):array
    {
        $option = Base\Arr::plus($this->attr(),$option);

        if(!empty($option['ping']) && is_int($option['ping']))
        $this->checkPing($option['ping'],$option['proxyHost'] ?? null,$option['proxyPort'] ?? null);

        $curl = $this->curl($option);
        $exec = Base\Curl::exec($curl);
        $throw = null;
        $code = null;

        if(empty($exec) || empty($exec['meta']['info']))
        $throw = ['requestFailed'];

        $info = $exec['meta']['info'];

        if(empty($throw) && (!empty($info['errorNo']) || !empty($info['error'])))
        $throw = [$info['errorNo'],$info['error']];

        if(empty($throw) && empty($exec['resource']))
        $throw = ['responseHasNoResource',$exec['meta']['error'] ?? null];

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
                $code ??= 0;
                static::catchable(null,'responseCodeShouldBe',$strCode,'not',$code);
            }
        }

        if(!empty($throw))
        static::throw(...$throw);

        return [
            'header'=>$exec['header'],
            'resource'=>$exec['resource'],
            'timestamp'=>Base\Datetime::now()
        ];
    }


    // trigger
    // trigger la requête et retourne un objet réponse
    final public function trigger(?array $option=null):Response
    {
        return Response::newOverload($this,$option);
    }


    // default
    // retourne le tableau des défauts pour une nouvelle requête
    final public function default(?array $value=null):array
    {
        $return = [];
        $value = Base\Arr::plus($this->getAttr('default'),$value);

        foreach ($value as $key => $value)
        {
            if(is_string($key))
            {
                if(is_string($value))
                $return[$key] = $value;

                elseif(static::isCallable($value))
                $return[$key] = $value();
            }
        }

        return $return;
    }


    // live
    // créer un objet requête à partir de la requête courante dans base request
    // la requête crée n'agit pas comme référence de la requête courante
    final public static function live():self
    {
        return new static(null);
    }
}
?>