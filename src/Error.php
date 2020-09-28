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

// error
// class used as a basic error handler
class Error extends Root
{
    // trait
    use _serialize;


    // config
    protected static array $config = [
        'lang'=>null, // langue de l'erreur
        'cast'=>'titleMessage', // méthode à utiliser pour cast
        'errorLog'=>true, // l'erreur est loggé dans php.log
        'output'=>true, // output à l'écran
        'outputDepth'=>3, // niveau de précision du output
        'traceArgs'=>false, // affiche des arguments dans trace
        'traceLength'=>[self::class,'getTraceLength'], // longueur du trace
        'traceLengthArray'=>5, // longueur du trace pour toArray
        'callback'=>null, // fonction de callback envoyé au début du trigger
        'cleanBuffer'=>null, // vide le buffer
        'com'=>false, // met l'erreur dans com
        'log'=>true, // envoie au log ou non
        'logClass'=>null, // classes pour log, peut être string ou array, s'il y a en plusieurs utilise seulement le premier qui fonctionne
        'kill'=>true, // fin de php
        'default'=>22, // code par défaut si vide
        'doc'=>['html'=>"data-error='fatal'",'head'=>null], // attribut pour les balises lors de la création de la page html, fournir des string pas de un array
        'throwMethod'=>'throwCommon', // nom de la méthode throw utilisé à travers quid
        'type'=>[ // description des types
            1=>['key'=>'error','name'=>'Error'],
            2=>['key'=>'notice','name'=>'Notice'],
            3=>['key'=>'deprecated','name'=>'Deprecated'],
            11=>['key'=>'assert','name'=>'Assertion'],
            21=>['key'=>'silent','name'=>'Silent','output'=>false,'kill'=>false,'cleanBuffer'=>false],
            22=>['key'=>'warning','name'=>'Warning','kill'=>false,'cleanBuffer'=>false],
            23=>['key'=>'fatal','name'=>'Fatal'],
            31=>['key'=>'exception','name'=>'Exception'],
            32=>['key'=>'catchableException','name'=>'Catchable Exception']]
    ];


    // lang
    protected static ?Lang $lang = null; // objet pour lang
    protected static ?Com $com = null; // objet pour com


    // dynamic
    protected ?string $message = null; // message de l'erreur
    protected ?int $code = null; // code de l'erreur
    protected ?string $file = null; // fichier de l'erreur
    protected ?int $line = null; // ligne de l'erreur
    protected array $trace = []; // trace de l'erreur
    protected $info = null; // info sur l'erreur
    protected ?array $stack = null; // stack pour les exceptions
    protected ?string $content = null; // contenu additionnelle pour les exceptions


    // construct
    // constructeur public de l'erreur
    final public function __construct($value=null,?int $code=null,?array $attr=null)
    {
        $this->makeAttr($attr);

        if($value instanceof \Throwable)
        $this->prepareThrowable($value);
        else
        $this->prepare($value,$code);
    }


    // invoke
    // appel de la classe, renvoie vers trigger
    final public function __invoke(...$args):self
    {
        return $this->trigger(...$args);
    }


    // toString
    // retourne le nom de l'erreur
    final public function __toString():string
    {
        return $this->titleMessage();
    }


    // toArray
    // retourne l'erreur sous une forme tableau
    // utilisé par logError
    final public function toArray():array
    {
        $return = [];
        $return['message'] = $this->getMessage();
        $return['code'] = $this->getCode();
        $return['file'] = $this->getFile();
        $return['line'] = $this->getLine();
        $return['trace'] = $this->getTrace($this->getAttr('traceLengthArray'),false);

        return $return;
    }


    // cast
    // retourne la valeur cast
    // la méthode utilisé pour cast peut être défini dans attr
    final public function _cast():string
    {
        $return = '';
        $method = $this->getAttr('cast');
        if(!empty($method))
        $return = $this->$method();

        return $return;
    }


    // isException
    // retourne vrai si l'erreur provient d'une exception
    final public function isException():bool
    {
        return $this->getCode() > 30;
    }


    // makeSilent
    // rend une erreur silencieuse
    final public function makeSilent():self
    {
        $this->setAttr('cleanBuffer',false);
        $this->setAttr('output',false);
        $this->setAttr('kill',false);

        return $this;
    }


    // id
    // retourne le id unique de l'erreur, avec le id de la réponse
    final public function id(?int $inc=null):string
    {
        return implode('-',[Base\Response::id(),$this->splId()]);
    }


    // basename
    // retourne le basename à utiliser avec l'erreur
    final public function basename(?int $inc=null):string
    {
        $return = '';
        $arr = [];
        $arr[] = Base\Datetime::format('Y_m_d_H_i_s');
        $arr[] = Base\Path::filename($this->getFile());
        $arr[] = $this->getLine();

        if(is_int($inc))
        $arr[] .= $inc;

        $return = implode('-',$arr);

        return $return;
    }


    // getMessage
    // retourne le message de l'erreur
    final public function getMessage():string
    {
        return $this->message;
    }


    // setMessage
    // enregistre le message de l'erreur
    final protected function setMessage(string $message):void
    {
        $this->message = $message;
    }


    // getCode
    // retourne le code de l'erreur
    final public function getCode():int
    {
        return $this->code;
    }


    // setCode
    // enregistre le code de l'erreur
    // les attrs sont mis à jour
    final protected function setCode(int $code):void
    {
        $this->code = $code;
        $this->attr($this->getType());
    }


    // getFile
    // retourne le fichier de l'erreur
    final public function getFile():string
    {
        return $this->file;
    }


    // setFile
    // enregistre le fichier de l'erreur
    final protected function setFile(string $file):void
    {
        $this->file = $file;
    }


    // getLine
    // retourne la ligne de l'erreur
    final public function getLine():int
    {
        return $this->line;
    }


    // setLine
    // enregistre la ligne de l'erreur
    final protected function setLine(int $line):void
    {
        $this->line = $line;
    }


    // getTrace
    // retoune la trace de l'erreur
    // les attrs traceLength et traceArgs sont utilisés ici si les arguments length et args ne sont pas spécifiés
    final public function getTrace(?int $length=null,?bool $args=null):array
    {
        $length ??= $this->getAttr('traceLength',true) ?? 1;
        $args ??= $this->getAttr('traceArgs') ?? false;
        $return = Base\Debug::traceSlice(0,$length,$this->getFile(),$this->getLine(),$args,$this->trace);

        return $return;
    }


    // getTraceLastCall
    // retourne la dernière fonction ou méthode appelée
    final public function getTraceLastCall():?string
    {
        return Base\Debug::traceLastCall($this->getFile(),$this->getLine(),$this->trace);
    }


    // setTrace
    // enregistre la trace dans l'objet
    // si le trace ne contient pas file et line, prépend le pour que le rendu trace soit similaires aux exceptions
    // si la première trace est une classe qui lance des exception via root/throw, enlève la classe, function et type du tableau trace
    final protected function setTrace(array $trace):void
    {
        $this->trace = $trace;
        $trace = $this->getTrace();

        if(empty($trace))
        {
            $unshift = ['file'=>$this->getFile(),'line'=>$this->getLine()];
            array_unshift($this->trace,$unshift);
        }
    }


    // getInfo
    // retourne la ou les info de l'erreur ou de l'exception
    // info est utilisé dans le titre pour erreur et exception
    final public function getInfo()
    {
        return $this->info;
    }


    // setInfo
    // change la ou les info lié à l'erreur
    // code de l'erreur php ou nom de la classe
    final protected function setInfo($value=null):void
    {
        if(is_scalar($value))
        $this->info = $value;
    }


    // getStack
    // retourne les parents de l'exception
    // retourne null si l'erreur n'est pas une exception
    final public function getStack():?array
    {
        return $this->stack;
    }


    // setStack
    // garde en mémoire le stack des exceptions
    final protected function setStack(array $value):void
    {
        $this->stack = $value;
    }


    // getContent
    // retourne le contenu additionnelle d'une exception étendu
    final public function getContent():?string
    {
        return $this->content;
    }


    // setContent
    // change le contenu additionnelle d'une exception étendu
    final protected function setContent(?string $value):void
    {
        $this->content = $value;
    }


    // prepare
    // prépare l'erreur, data peut être throwable, array ou string
    // va chercher le dernier fichier et numéro de ligne si ces paramètres ne sont pas dans précisés
    final protected function prepare($value=null,?int $code=null):void
    {
        if(is_string($value))
        $value = [$value];

        elseif(!is_array($value))
        $value = [];

        if(is_array($value))
        {
            $message = Base\Arr::keysFirstValue(['message',0],$value);
            $file = Base\Arr::keysFirstValue(['file',1],$value);
            $line = Base\Arr::keysFirstValue(['line',2],$value);
            $info = Base\Arr::keysFirstValue(['info',3],$value);
            $trace = (array_key_exists('trace',$value) && is_array($value['trace']))? $value['trace']:Base\Debug::trace();

            if(empty($code))
            $code = (array_key_exists('code',$value) && is_int($value['code']) && !empty($value['code']))? $value['code']:static::defaultCode();

            if(!is_string($message))
            $message = '';

            if(!is_string($file) || !is_int($line))
            {
                $traceBefore = Base\Debug::traceBeforeClass([static::class,self::class],true);
                if(!empty($trace) && array_key_exists('file',$traceBefore) && array_key_exists('line',$traceBefore))
                {
                    $file = $traceBefore['file'];
                    $line = $traceBefore['line'];
                }
            }

            $this->setCode($code);
            $this->setMessage($message);
            $this->setFile($file);
            $this->setLine($line);
            $this->setTrace($trace);
            $this->setInfo($info);
        }
    }


    // prepareThrowable
    // prépare l'erreur, data doit être throwable
    // prend file et line de trace index 0 si l'exception a été crée dans les classes exception ou root (par les méthodes throw)
    final protected function prepareThrowable(\Throwable $value):void
    {
        $code = static::grabCode($value);
        $trace = $value->getTrace();
        $file = $value->getFile();
        $line = $value->getLine();
        $info = get_class($value);
        $content = (method_exists($value,'content'))? $value->content():null;
        $stack = Exception::stack($value);

        if($value instanceof Exception)
        {
            $lang = static::getLang();
            $throwMethod = $this->getAttr('throwMethod');

            if(!empty($lang))
            {
                $message = $value->getMessageArgs($lang,false);
                if(!empty($message))
                $this->setAttr('cast','getMessage');
            }

            if(!empty($trace[0]['function']) && $trace[0]['function'] === $throwMethod)
            {
                $index = (array_key_exists(1,$trace))? 1:0;
                $file = $trace[$index]['file'];
                $line = $trace[$index]['line'];
            }
        }

        if(empty($message))
        $message = $value->getMessage();

        $this->setCode($code);
        $this->setMessage($message);
        $this->setFile($file);
        $this->setLine($line);
        $this->setTrace($trace);
        $this->setInfo($info);
        $this->setStack($stack);
        $this->setContent($content);
    }


    // type
    // retourne le tableau de type pour l'erreur
    final public function getType():array
    {
        return $this->getAttr(['type',$this->getCode()]) ?? [];
    }


    // getKey
    // retourne la clé de l'erreur
    final public function getKey():?string
    {
        $return = null;
        $key = $this->getAttr('key');

        if(is_string($key) && strlen($key))
        $return = $key;

        return $return;
    }


    // title
    // retourne le titre à afficher pour l'erreur
    // met aussi le code de l'erreur en paranthèse
    final public function title():string
    {
        $return = $this->name();

        $info = $this->getInfo();

        if(is_scalar($info))
        {
            if(is_int($info))
            $info = Base\Error::code($info,$this->getAttr('lang'));

            if(is_string($info) && !empty($info))
            {
                if(strlen($return))
                $return .= ': ';

                $return .= $info;
            }
        }

        $code = $this->getCode();
        $return .= " (#$code)";

        return $return;
    }


    // titleMessage
    // retourne le titre avec le message
    final public function titleMessage(string $separator='->'):string
    {
        $return = $this->title();
        $return .= ' -> ';
        $return .= $this->getMessage();

        return $return;
    }


    // name
    // retourne le nom du code d'erreur
    final public function name():string
    {
        $return = '';
        $code = $this->getCode();

        if(is_int($code))
        {
            $langInst = static::getLang();
            if(!empty($langInst))
            {
                $label = $langInst->errorLabel($code,$this->getAttr('lang'));
                if(is_string($label))
                $return = $label;
            }

            if(empty($return))
            {
                $type = static::getType();

                if(!empty($type) && array_key_exists('name',$type) && is_string($type['name']))
                $return = $type['name'];
            }
        }

        return $return;
    }


    // trigger
    // lance les actions liés à l'erreur
    final public function trigger(bool $callback=true):self
    {
        if($callback === true)
        Base\Call::back('callback',$this->attr(),$this);

        return $this->dispatch();
    }


    // dispatch
    // fait le dispatch
    final protected function dispatch():self
    {
        // errorLog
        if($this->getAttr('errorLog') === true)
        $this->errorLog();

        // log
        if($this->getAttr('log') === true)
        $this->log();

        // com
        if($this->getAttr('com') === true)
        $this->com();

        // cleanBuffer
        if($this->getAttr('cleanBuffer') === true)
        Base\Buffer::cleanAll();

        // output
        if($this->getAttr('output') === true)
        $this->output();

        // kill
        if($this->getAttr('kill') === true)
        Base\Response::kill();

        return $this;
    }


    // errorLog
    // écrit le message d'erreur log
    final protected function errorLog():self
    {
        Base\Error::log($this->getOutputArray(null,false));

        return $this;
    }


    // log
    // queue le log dans la ou les classes fournis en attr
    // un seul log est effectué, donc passe seulement au prochain si le return est null
    final public function log():?Contract\Log
    {
        $return = null;
        $logs = $this->getAttr('logClass');

        if(!empty($logs))
        {
            if(!is_array($logs))
            $logs = [$logs];

            foreach ($logs as $log)
            {
                if(is_string($log) && is_a($log,Contract\Log::class,true))
                {
                    $return = $log::log($this);

                    if(!empty($return))
                    break;
                }
            }
        }

        return $return;
    }


    // com
    // ajoute un contenu dans l'objet com si disponible
    final public function com():self
    {
        $com = static::getCom();
        if(!empty($com))
        $com->error($this);

        return $this;
    }


    // output
    // fait un output de l'erreur
    // output différent si c'est cli ou non
    final public function output():void
    {
        if(Base\Server::isCli())
        $this->outputCli();

        else
        $this->outputHtml();
    }


    // getOutput
    // retourne le output de l'erreur
    final public function getOutput():string
    {
        $return = '';

        if(Base\Server::isCli())
        $return .= $this->cli();

        else
        $return .= $this->html();

        return $return;
    }


    // outputCli
    // envoie le output pour le cli
    final public function outputCli():void
    {
        Base\Buffer::flushEcho($this->cli());
    }


    // cli
    // génère le output pour le cli
    final public function cli():string
    {
        $return = '';

        foreach ($this->getOutputArray() as $k => $v)
        {
            $preset = ($k <= 2)? 'neg':'neutral';
            $return .= Cli::preset($preset,$v);
        }

        return $return;
    }


    // outputHtml
    // envoie le output pour le html
    final public function outputHtml():void
    {
        Base\Response::serverError();

        $buffer = Base\Buffer::get();
        $html = $this->html();

        if(empty($buffer))
        {
            $doc = $this->getAttr('doc');
            $html = Html::docSimple($this->title(),$html,$doc);
        }

        Base\Buffer::flushEcho($html);
    }


    // html
    // génère le html de l'erreur
    final public function html($outputDepth=null):string
    {
        $return = '';

        foreach ($this->getOutputArray($outputDepth) as $k => $v)
        {
            // stack
            if($k === 5)
            $return .= "<pre>$v</pre>";

            // trace
            elseif($k === 6)
            $return .= $v;

            // id
            elseif($k === 7)
            $return .= "<h6>$v</h6>";

            // autre
            else
            {
                $v = Html::specialChars($v);
                $return .= "<h$k>$v</h$k>";
            }
        }

        return $return;
    }


    // makeOutputArray
    // retourne le tableau des valeurs pour le output de l'erreur
    final public function makeOutputArray(bool $showTrace=true):array
    {
        $return = [];

        // title
        $title = $this->title();
        if(strlen($title))
        $return[1] = $title;

        // message
        $message = $this->getMessage();
        if(strlen($message))
        $return[2] = '«'.$message.'»';

        // file, line, lastCall
        $file = $this->getFile();
        $line = $this->getLine();
        $traceLastCall = $this->getTraceLastCall();
        if(strlen($file) && !empty($line))
        {
            $string = $file.'::'.$line;
            if(is_string($traceLastCall) && strlen($traceLastCall))
            $string .= " -> $traceLastCall()";
            $return[3] = $string;
        }

        // content (pour exception), stack et trace
        if($showTrace === true)
        {
            if($this->isException())
            {
                $content = $this->getContent();
                if(!empty($content))
                $return[4] = $content;
            }

            $stack = $this->getStack();
            if(!empty($stack))
            {
                $return[5] = '';

                foreach ($stack as $value)
                {
                    $return[5] .= (!empty($return[5]))? PHP_EOL:'';
                    $return[5] .= Exception::output($value);
                }
            }

            $trace = $this->getTrace();
            if(!empty($trace))
            $return[6] = Base\Debug::varGet($trace);
        }

        // responseId
        $return[7] = '#'.$this->id();

        return $return;
    }


    // getOutputArray
    // retourne les entrées du tableau de output qu'il faut afficher selon l'attr outputDepth
    final public function getOutputArray($outputDepth=null,bool $showTrace=true):array
    {
        $return = [];
        $outputDepth ??= $this->getAttr('outputDepth');

        if(!empty($outputDepth))
        {
            foreach ($this->makeOutputArray($showTrace) as $k => $v)
            {
                if(is_string($v) && ($outputDepth === true || $k <= $outputDepth))
                $return[$k] = $v;
            }
        }

        return $return;
    }


    // handler
    // methode pour set_error_handler
    final public static function handler(int $errorCode,string $message,string $file,int $line,$arg=null,?array $attr=null):?self
    {
        $return = null;
        $errorReporting = Base\Error::reporting();

        if($errorReporting !== 0)
        {
            $code = static::grabCode($errorCode);
            $error = new static([$message,$file,$line,$errorCode],$code,$attr);

            $return = $error->trigger();
        }

        return $return;
    }


    // exception
    // méthode pour les exceptions
    final public static function exception(\Throwable $throwable,?array $attr=null):self
    {
        $error = new static($throwable,null,$attr);

        return $error->trigger();
    }


    // assert
    // methode pour assert_callback
    final public static function assert(string $file,int $line,$code=null,?string $message=null,?array $attr=null):self
    {
        $code = static::grabCode('assert');
        $error = new static([$message,$file,$line],$code,$attr);

        return $error->trigger();
    }


    // make
    // crée une erreur erreurs avec type -> silent, recoverable et fatal
    final protected static function make(string $type,string $message,?array $attr=null):self
    {
        $code = static::grabCode($type);
        $error = new static($message,$code,$attr);

        return $error->trigger();
    }


    // silent
    // gère une erreur silencieuse
    final public static function silent(string $value,?array $attr=null):self
    {
        return static::make('silent',$value,$attr);
    }


    // warning
    // gère une erreur warning
    final public static function warning(string $value,?array $attr=null):self
    {
        return static::make('warning',$value,$attr);
    }


    // fatal
    // gère une erreur fatal
    final public static function fatal(string $value,?array $attr=null):self
    {
        return static::make('fatal',$value,$attr);
    }


    // defaultCode
    // retourne le code par défaut dans config, s'applique aussi aux exceptions sans code
    final public static function defaultCode():?int
    {
        return static::$config['default'] ?? null;
    }


    // grabCode
    // retourne le code de l'erreur
    // value peut être int, string ou throwable
    // string peut être default
    final public static function grabCode($value):int
    {
        $return = null;
        $key = null;

        if($value instanceof \Throwable)
        {
            $key = 'exception';
            $return = $value->getCode();
        }

        if(!is_int($return) || empty($return))
        {
            if(is_int($value))
            {
                $key = 'error';

                if($value === E_NOTICE || $value === E_USER_NOTICE)
                $key = 'notice';

                elseif($value === E_DEPRECATED || $value === E_USER_DEPRECATED)
                $key = 'deprecated';
            }

            elseif(is_string($value))
            $key = $value;

            if(is_string($key))
            {
                foreach (static::$config['type'] as $k => $v)
                {
                    if(is_array($v) && array_key_exists('key',$v) && $v['key'] === $key)
                    {
                        $return = $k;
                        break;
                    }
                }
            }

            if(!is_int($return) || empty($return))
            $return = static::defaultCode();
        }

        return $return;
    }


    // getLang
    // retourne la lang liée à la classe erreur
    final public static function getLang():?Lang
    {
        return static::$lang;
    }


    // setLang
    // lie une lang à la classe erreur
    final public static function setLang(?Lang $lang):void
    {
        static::$lang = $lang;
    }


    // getCom
    // retourne la com liée à la classe erreur
    final public static function getCom():?Com
    {
        return static::$com;
    }


    // setCom
    // lie un objet com à la classe erreur
    final public static function setCom(?Com $com):void
    {
        static::$com = $com;
    }


    // boolToOutputDepth
    // retourne la depth à utiliser à partir d'un boolean
    final public static function boolToOutputDepth(bool $value):int
    {
        if($value === true)
        $return = 7;

        elseif($value === false)
        $return = 2;

        return $return;
    }


    // setDefaultOutputDepth
    // change la valeur par défaut du output depth dans attr avant la création de l'objet erreur
    final public static function setDefaultOutputDepth($value):void
    {
        if(is_bool($value))
        $value = static::boolToOutputDepth($value);

        if(is_int($value))
        static::$config['outputDepth'] = $value;
    }


    // getTraceLength
    // callback pour retourner la longueur du trace
    final public static function getTraceLength():int
    {
        return (Base\Server::isCli())? 10:20;
    }


    // setDocHead
    // permet d'ajouter du contenu dans le head html pour les erreurs fatales, par exemple un fichier css
    final public static function setDocHead(string $value):void
    {
        static::$config['doc']['head'] = $value;
    }


    // init
    // initialise la prise en charge des erreurs, exception et assertion
    public static function init():void
    {
        Base\Error::clearLast();
        Base\Error::setHandler([static::class,'handler']);
        Base\Exception::setHandler([static::class,'exception']);
        Base\Assert::setHandler([static::class,'assert']);
        Base\Uri::setNotFound([CatchableException::class,'throw']);
        Base\File::setNotFound([CatchableException::class,'throw']);
        Base\Obj::setCastError([Exception::class,'throw']);
    }
}
?>