<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main;
use Quid\Base;

// lang
// class for a collection object containing language texts and translations
class Lang extends Map
{
    // trait
    use Map\_arrs;
    use _inst;
    use _option;


    // config
    public static $config = [
        'pattern'=>'%', // caractère utilisé pour option text pattern
        'option'=>[
            'onLoad'=>null, // callback utilisé lors de l'appel à la méthode load, cette méthode est appelé lorsque le tableau d'une langue est vide
            'wrapKeys'=>'[]', // wrap les clés lors du replace
            'wrapPluralKeys'=>'%', // wrap les clés pour le replace pluriel
            'text'=>[ // option pour text
                'case'=>null, // méthode qui permet de changer le case du text si trouvé
                'pattern'=>null, // permet de spécifier un pattern de retour, mettre le texte dans une autre string via %, si pattern est int c'est lié à excerpt (longueur)
                'notFound'=>true, // la méthode text utilise textNotFound, doit être true
                'error'=>true, // envoie une exception si le text n'est pas trouvé
                'alt'=>null, // cherche une deuxième valeur text si introuvable
                'def'=>null, // retourne la clé passé dans dans keyPrepare si introuvable entouré de bracket
                'same'=>null, // retourne la clé passé dans dans keyPrepare
                'other'=>null, // cherche la valeur dans une autre langue si pas trouvé, peut être int ou string
                'html'=>null]], // enrobbe le text dans une tag html
        'case'=>[
            'char'=>'|', // caractère utilisé pour case
            'callable'=>[ // lien entre raccourcis et callable pour le changement de case
                'lc'=>[Base\Str::class,'lower'],
                'uc'=>[Base\Str::class,'upper'],
                'lcf'=>[Base\Str::class,'lowerFirst'],
                'ucf'=>[Base\Str::class,'upperFirst'],
                'tc'=>[Base\Str::class,'title']]],
        'path'=>[ // chemin pour des types de texte précis liés à des méthodes
            'numberFormat'=>'number/format',
            'numberPercentFormat'=>'number/percentFormat',
            'numberMoneyFormat'=>'number/moneyFormat',
            'numberPhoneFormat'=>'number/phoneFormat',
            'numberSizeFormat'=>'number/sizeFormat',
            'dateMonth'=>'date/month',
            'dateFormat'=>'date/format',
            'dateStr'=>'date/str',
            'datePlaceholder'=>'date/placeholder',
            'dateDay'=>'date/day',
            'dateDayShort'=>'date/dayShort',
            'headerResponseStatus'=>'header/responseStatus',
            'errorCode'=>'error/code',
            'errorLabel'=>'error/label',
            'com'=>'com',
            'pos'=>'com/pos',
            'neg'=>'com/neg']
    ];


    // map
    protected static $allow = ['sort','set','unset','remove','empty','replace','overwrite','serialize','clone']; // méthodes permises


    // dynamique
    protected $current = null; // lang courante


    // construct
    // construit l'objet lang, fournir toutes les langs sans contenu en premier argument
    public function __construct($all,?array $option=null)
    {
        $this->setLang(null,$all);
        $this->option($option);

        return;
    }


    // invoke
    // invoke est utiliser comme alias de getAppend
    public function __invoke(...$args)
    {
        return $this->getAppend(...$args);
    }


    // onPrepareSetInst
    // méthode appeler lors de setInst
    // méthode protégé
    protected function onPrepareSetInst():self
    {
        if(Base\Lang::hasCallable())
        static::throw('baseLangAlreadyHasAnInst');

        return $this;
    }


    // onSetInst
    // callback après l'ajout de lang dans inst
    // méthode protégé
    protected function onSetInst():self
    {
        Base\Lang::setCallable($this->getCallable());
        $this->onChange();

        $class = Error::getOverloadClass();
        $class::setLang($this);

        return $this;
    }


    // onPrepareUnsetInst
    // méthode appeler lors de unsetInst
    // méthode protégé
    protected function onPrepareUnsetInst():self
    {
        if(!Base\Lang::hasCallable())
        static::throw('baseLangDoesNotHaveAPrimary');

        elseif(!Base\Lang::isCallable($this->getCallable()))
        static::throw('baseLangHasAnotherPrimary');

        return $this;
    }


    // onUnsetInst
    // callback après le retrait de lang de inst
    // méthode protégé
    protected function onUnsetInst():self
    {
        Base\Lang::unsetCallable();

        $class = Error::getOverloadClass();
        $class::setLang(null);

        return $this;
    }


    // onChange
    // callback après un ajout, rettait ou changement de langue
    // déclenche root lang set si l'objet est primaire et valide
    // méthode protégé
    protected function onChange():void
    {
        if($this->inInst())
        {
            $this->checkInst();
            Base\Lang::set($this->currentLang(),$this->allLang());
        }

        return;
    }


    // onPrepareReplace
    // prépare le contenu pour une méthode de remplacement comme overwrite, replace ou splice
    protected function onPrepareReplace($value)
    {
        return parent::onPrepareReplace(Base\Lang::content($value));
    }


    // baseCall
    // méthode utiliser par baseLang
    // appel une méthode de la classe qui peut retourner un array ou une valeur
    public function baseCall(string $method,...$args)
    {
        return $this->$method(...$args);
    }


    // arr
    // retourne une référence du tableau à la langue courante, possibilité de spécifier la langue
    // lance la méthode load si le contenu de lang est null
    // méthode protégé pour empêcher des modifications de l'objet par l'extérieur
    protected function &arr(?string $lang=null):array
    {
        $return = [];
        $this->onCheckArr();
        $lang = ($lang === null)? $this->currentLang():$lang;

        if(array_key_exists($lang,$this->data))
        {
            if(!is_array($this->data[$lang]))
            $this->load($lang);

            $return =& $this->data[$lang];
        }

        else
        static::throw('invalidLangCode');

        return $return;
    }


    // load
    // permet de charger le contenu d'une lang
    // la méthode utilise la callback onLoad défini dans les options
    // une exception est envoyé si la lang n'existe pas
    public function load(string $value):self
    {
        $this->checkLang($value);
        $value = Base\Lang::prepareCode($value);
        $content = [];
        $onLoad = $this->getOption('onLoad');

        if(static::classIsCallable($onLoad))
        {
            $onLoad = $onLoad($value);

            if(is_array($onLoad))
            $content = $onLoad;
        }

        $this->data[$value] = $content;

        return $this;
    }


    // isLang
    // retourne vrai si la valeur est une lang de l'objet
    public function isLang($value):bool
    {
        $return = false;
        $value = Base\Lang::prepareCode($value);

        if(!empty($value) && in_array($value,$this->allLang(),true))
        $return = true;

        return $return;
    }


    // checkLang
    // envoie une exception si la langue n'existe pas dans l'objet
    public function checkLang($value):self
    {
        if(!$this->isLang($value))
        static::throw();

        return $this;
    }


    // isLangLoaded
    // retourne vrai si la lang existe et le contenu a été chargé
    public function isLangLoaded($value):bool
    {
        $return = false;

        if($value === null)
        $value = $this->currentLang();

        if($this->isLang($value) && is_array($this->data[$value]))
        $return = true;

        return $return;
    }


    // isCurrent
    // retourne vrai si la langue courante est la valeur
    public function isCurrent($value):bool
    {
        return (Base\Lang::prepareCode($value) === $this->currentLang())? true:false;
    }


    // isOther
    // retourne vrai si la langue est dans l'objet et n'est pas la langue courante
    public function isOther($value):bool
    {
        return ($this->isLang($value) && !$this->isCurrent($value))? true:false;
    }


    // currentLang
    // retourne la langue courante de l'objet
    public function currentLang():string
    {
        return $this->current;
    }


    // defaultLang
    // retourne la langue par défaut de l'objet
    public function defaultLang():string
    {
        return current($this->allLang());
    }


    // otherLang
    // retourne une langue autre par index ou string
    // possibilité de mettre une autre langue courante en deuxième argument
    public function otherLang($arg=0,?string $value=null):?string
    {
        $return = null;
        $others = $this->othersLang($value);
        $arg = ($arg === true)? 0:$arg;

        if(is_int($arg) && array_key_exists($arg,$others))
        $return = $others[$arg];

        elseif(is_string($arg) && in_array($arg,$others,true))
        $return = $arg;

        return $return;
    }


    // othersLang
    // retourne le tableau des autres langues
    // possibilité de mettre une autre langue courante en deuxième argument
    public function othersLang(?string $value=null):array
    {
        $return = [];
        $value = $this->codeLang($value);
        $return = Base\Arr::valueStrip($value,$this->allLang());
        $return = array_values($return);

        return $return;
    }


    // allLang
    // retourne toutes les langs de l'objet
    public function allLang()
    {
        return array_keys($this->data);
    }


    // countLang
    // retourne le nombre de lang dans l'objet
    public function countLang():int
    {
        return count($this->allLang());
    }


    // codeLang
    // retourne le code formatté ou la langue courante si le code formatté est invalide
    public function codeLang(?string $value=null):string
    {
        $return = Base\Lang::prepareCode($value);

        if(!is_string($return))
        $return = $this->currentLang();

        return $return;
    }


    // setLang
    // permet d'ajouter les langues et de changer la langue courante
    // vide le tableau all avant de faire l'ajout et changement
    // exception envoyé en cas d'erreur
    public function setLang(?string $value,$all):self
    {
        $current = Base\Lang::prepareCode($value);

        if(is_string($all))
        $all = [$all];

        if(is_array($all) && !empty($all) && ($value === null || in_array($value,$all,true)))
        {
            $this->data = [];
            $this->addLang(...array_values($all));

            if($value === null)
            $value = $this->defaultLang();

            $this->changeLang($value);
        }

        else
        static::throw();

        return $this;
    }


    // addLang
    // ajoute une ou plusieurs langues
    // exception envoyé en cas d'erreur
    // callback onChange lancé pour chaque ajout
    public function addLang(string ...$values):self
    {
        foreach ($values as $value)
        {
            $value = Base\Lang::prepareCode($value);

            if(is_string($value) && !$this->isLang($value))
            {
                $this->data[$value] = null;
                $this->onChange();
            }

            else
            static::throw();
        }

        return $this;
    }


    // removeLang
    // enlève une ou plusieurs langues
    // exception envoyé en cas d'erreur ou si on tente d'enlever la langue courante
    // callback onChange lancé pour chaque retrait
    public function removeLang(string ...$values):self
    {
        foreach ($values as $value)
        {
            $value = Base\Lang::prepareCode($value);

            if(is_string($value) && $this->isLang($value))
            {
                if($this->isCurrent($value))
                static::throw('cannotRemoveCurrentLang');

                else
                {
                    unset($this->data[$value]);
                    $this->onChange();
                }
            }

            else
            static::throw();
        }

        return $this;
    }


    // changeLang
    // change la langue courante si la nouvelle lang existe et n'est pas la courante
    // exception envoyé en cas de langue non existante, pas d'erreur si on change pour la langue courante
    // callback onChange lancé en cas de succès
    public function changeLang(string $value):self
    {
        $value = Base\Lang::prepareCode($value);
        $current = $this->current;

        if($this->isLang($value))
        {
            if($value !== $current)
            {
                $this->current = $value;
                $this->onChange();
            }
        }

        else
        static::throw();

        return $this;
    }


    // getCallable
    // retourne la callable à utiliser pour BaseLang
    public function getCallable():callable
    {
        return [$this,'baseCall'];
    }


    // checkInst
    // envoie une exception si l'objet n'est pas la primaire ou si BaseLang a une autre primaire
    public function checkInst():self
    {
        if(!($this->inInst() && Base\Lang::isCallable($this->getCallable())))
        static::throw('objectIsNotPrimary',(Base\Lang::hasCallable())? 'baseLangHasAnotherPrimary':'baseLangHasNoPrimary');

        return $this;
    }


    // take
    // permet de prendre une valeur dans la langue courante ou une autre langue
    // cette méthode est la base des méthodes textes plus avancés
    // la clé est passé dans onPrepareKey qui est passé dans base/obj Cast
    public function take($key,?string $lang=null)
    {
        return Base\Arrs::get($this->onPrepareKey($key),$this->arr($lang));
    }


    // existsAppend
    // vérifie si une valeur existe dans la langue courante après avoir append plusieurs clés en argument
    public function existsAppend(...$keys):bool
    {
        $return = false;
        $append = Base\Arrs::keyPrepares(...$this->prepareKeys(...$keys));

        if(!empty($append))
        $return = $this->exists($append);

        return $return;
    }


    // existsTake
    // vérifie si une valeur existe, possibilité de mettre la langue en deuxième argument
    public function existsTake($key,?string $lang=null):bool
    {
        return Base\Arrs::keyExists($this->onPrepareKey($key),$this->arr($lang));
    }


    // existsText
    // vérifie si une valeur existe et est scalar, possibilité de mettre la langue en deuxième argument
    public function existsText($key,?string $lang=null):bool
    {
        return (is_scalar($this->take($key,$lang)))? true:false;
    }


    // existsTextAppend
    // vérifie si une valeur existe et est scalar après avoir append tous les arguments
    public function existsTextAppend(...$keys):bool
    {
        $return = false;
        $append = Base\Arrs::keyPrepares(...$this->prepareKeys(...$keys));

        if(!empty($append))
        $return = $this->existsText($append);

        return $return;
    }


    // takes
    // retourne plusieurs éléments du tableau de langue
    public function takes(array $keys,?string $lang=null):array
    {
        $return = [];

        foreach ($keys as $key)
        {
            $key = Base\Arrs::keyPrepare($this->onPrepareKey($key));
            $return[$key] = $this->take($key,$lang);
        }

        return $return;
    }


    // takeUnpack
    // retourne un élément de langue
    // l'argument est un tableau pack
    public function takeUnpack(array $array)
    {
        return $this->take(...array_values($array));
    }


    // getAppend
    // permet de faire un appel à get après avoir append tous les arguments
    public function getAppend(...$keys)
    {
        $return = null;
        $append = Base\Arrs::keyPrepares(...$this->prepareKeys(...$keys));

        if(!empty($append))
        $return = $this->get($append);

        return $return;
    }


    // getAll
    // retourne l'élément dans toutes les langues
    public function getAll($key):array
    {
        $return = [];

        foreach ($this->allLang() as $lang)
        {
            $return[$lang] = $this->take($key,$lang);
        }

        return $return;
    }


    // getOthers
    // retourne l'élément dans toutes les autres langues que la courante
    public function getOthers($key):array
    {
        $return = [];

        foreach ($this->othersLang() as $lang)
        {
            $return[$lang] = $this->take($key,$lang);
        }

        return $return;
    }


    // text
    // retourne un élément de langue
    // permet le replace et les options
    // la méthode doit retourner string, cast automatiquement les scalar en string
    public function text($key,?array $replace=null,?string $lang=null,?array $option=null):?string
    {
        $return = null;
        $option = $this->textOption($option);
        $case = static::$config['case']['char'];

        if(is_string($key) && is_string($case) && strpos($key,$case) !== false)
        {
            $explode = explode($case,$key);
            if(count($explode) === 2)
            {
                $option['case'] = $explode[0];
                $key = $explode[1];
            }
        }

        $value = $this->take($key,$lang);

        if(is_scalar($value))
        {
            $return = (string) $value;

            if(!empty($replace))
            $return = $this->textReplace($replace,$return,$option);
        }

        elseif($option['notFound'] === true)
        $return = $this->textNotFound($key,$replace,$lang,$option);

        if(is_string($return))
        $return = $this->textAfter($return,$option);

        return $return;
    }


    // textAfter
    // gère les options html et pattern pour une string texte
    // gère aussi les options case
    public function textAfter(string $return,?array $option=null):string
    {
        if(!empty($option['case']) && is_string($option['case']) && array_key_exists($option['case'],static::$config['case']['callable']))
        {
            $callable = static::$config['case']['callable'][$option['case']];
            if(!empty($callable))
            $return = $callable($return);
        }

        if(!empty($option['html']))
        $return = Base\Html::arg($return,$option['html']);

        if(isset($option['pattern']))
        {
            if(is_string($option['pattern']))
            $return = Base\Str::replace([static::$config['pattern']=>$return],$option['pattern']);

            elseif(is_int($option['pattern']))
            $return = Base\Str::excerpt($option['pattern'],$return);
        }

        return $return;
    }


    // textOption
    // retourne le tableau d'option pour la méthode text
    public function textOption(?array $option=null):array
    {
        return Base\Arr::plus(static::$config['option']['text'],$this->option['text'] ?? null,$option);
    }


    // textReplace
    // gère le remplacement pour une requête text réussie
    // méthode protégé
    protected function textReplace(array $replace,string $return,array $option):string
    {
        if(!empty($replace))
        {
            $replace = Base\Obj::cast($replace);
            $wrapKeys = $this->getOption('wrapKeys');

            if(!empty($wrapKeys))
            {
                $delimiter = Base\Segment::getDelimiter($wrapKeys);
                $replace = Base\Arr::keysWrap($delimiter[0],$delimiter[1],$replace);
            }

            $return = Base\Str::replace($replace,$return);
        }

        return $return;
    }


    // textNotFound
    // gère le résultat d'une requête text non trouvé
    // méthode protégé
    protected function textNotFound($key,?array $replace=null,?string $lang=null,array $option):?string
    {
        $return = null;
        $key = Base\Arrs::keyPrepare($key);

        if(!empty($key) && is_string($key))
        {
            if(!empty($option['alt']))
            {
                $text = $this->text($option['alt'],$replace,$lang,Base\Arr::plus($option,['html'=>null,'pattern'=>null,'notFound'=>false]));
                if(is_string($text))
                $return = $text;
            }

            if(!is_string($return) && is_scalar($option['other']))
            {
                $other = $this->otherLang($option['other']);
                if(is_string($other))
                {
                    $text = $this->text($key,$replace,$other,Base\Arr::plus($option,['html'=>null,'pattern'=>null,'notFound'=>false]));
                    if(is_string($text))
                    $return = $text;
                }
            }

            if(!is_string($return))
            {
                if($option['same'] === true)
                $return = $key;

                elseif($option['def'] === true)
                $return = "[$key]";

                elseif($option['error'] === true)
                {
                    if(empty($this->take($key,$lang)))
                    static::throw($key);
                    else
                    static::throw('requiresString',$key);
                }
            }
        }

        return $return;
    }


    // textAppendOne
    // permet de faire un appel à text avec deux arguments key qui sont append
    // possibilité de changer replace, lang et option
    public function textAppendOne($key,$key2,?array $replace=null,?string $lang=null,?array $option=null):?string
    {
        $return = null;
        $append = Base\Arrs::keyPrepares($key,$key2);

        if(!empty($append))
        $return = $this->text($append,$replace,$lang,$option);

        return $return;
    }


    // textAppend
    // permet de faire un appel à text avec un nombre illimité d'arguments qui s'append
    // pas de possibilité de changer replace, lang ou option
    public function textAppend(...$keys):?string
    {
        $return = null;
        $append = Base\Arrs::keyPrepares(...$keys);

        if(!empty($append))
        $return = $this->text($append);

        return $return;
    }


    // textUnpack
    // retourne un élément de langue text avec possibilité de replace et option
    // l'argument est un tableau pack
    public function textUnpack(array $array):?string
    {
        return $this->text(...array_values($array));
    }


    // textAll
    // retourne un élément text dans toutes les langues
    public function textAll($key,?array $replace=null,?array $option=null):array
    {
        $return = [];

        foreach ($this->allLang() as $lang)
        {
            $return[$lang] = $this->text($key,$replace,$lang,$option);
        }

        return $return;
    }


    // textOthers
    // retourne un élément text dans toutes les autres langues que la courante
    public function textOthers($key,?array $replace=null,?array $option=null):array
    {
        $return = [];

        foreach ($this->othersLang() as $lang)
        {
            $return[$lang] = $this->text($key,$replace,$lang,$option);
        }

        return $return;
    }


    // plural
    // fait une méthode text plural
    // le résultat valide de text est passé à plural
    // value peut être int ou array
    // le premier remplacement est remplacement de segment
    // l'argument plural est le remplacement pour pluriel
    public function plural($value,$key,?array $replace=null,?array $plural=null,?string $lang=null,?array $option=null):?string
    {
        return Base\Str::plural($value,$this->text($key,$replace,$lang,$option),$plural,null,$this->getOption('wrapPluralKeys'));
    }


    // html
    // fait une méthode text
    // le résultat valide est passé dans Base\Html::arg
    public function html($html,$key,?array $replace=null,?string $lang=null,?array $option=null):?string
    {
        return $this->text($key,$replace,$lang,Base\Arr::plus($option,['html'=>$html]));
    }


    // pattern
    // fait une méthode text
    // le résultat valide est remplacé dans une string pattern
    public function pattern($pattern,$key,?array $replace=null,?string $lang=null,?array $option=null):?string
    {
        return $this->text($key,$replace,$lang,Base\Arr::plus($option,['pattern'=>$pattern]));
    }


    // strict
    // fait une méthode text
    // le résultat doit être valide ou une exception est envoyé
    public function strict($key,?array $replace=null,?string $lang=null,?array $option=null):?string
    {
        return $this->text($key,$replace,$lang,Base\Arr::plus($option,['notFound'=>true,'error'=>true,'def'=>false,'same'=>false,'alt'=>null,'other'=>null]));
    }


    // safe
    // fait une méthode text
    // retourne null si introuvé, aucune erreur envoyé
    public function safe($key,?array $replace=null,?string $lang=null,?array $option=null):?string
    {
        return $this->text($key,$replace,$lang,Base\Arr::plus($option,['error'=>false]));
    }


    // alt
    // fait une méthode text
    // si le résultat est invalide, fait une requête alternative
    public function alt($key,$alt,?array $replace=null,?string $lang=null,?array $option=null):?string
    {
        return $this->text($key,$replace,$lang,Base\Arr::plus($option,['alt'=>$alt]));
    }


    // other
    // fait une méthode text
    // si le résultat est invalide, fait la requête dans une autre langue
    public function other($key,$other=0,?array $replace=null,?string $lang=null,?array $option=null):?string
    {
        return $this->text($key,$replace,$lang,Base\Arr::plus($option,['other'=>$other]));
    }


    // def
    // fait une méthode text
    // si le résultat est invalide, retourne la clé entière sous forme de string entouré de bracket
    public function def($key,?array $replace=null,?string $lang=null,?array $option=null):?string
    {
        return $this->text($key,$replace,$lang,Base\Arr::plus($option,['def'=>true]));
    }


    // same
    // fait une méthode text
    // si le résultat est invalide, retourne la clé entière sous forme de string
    public function same($key,?array $replace=null,?string $lang=null,?array $option=null):?string
    {
        return $this->text($key,$replace,$lang,Base\Arr::plus($option,['same'=>true]));
    }


    // translate
    // traduit une valeur d'une langue à une autre
    // retourne un tableau clé -> valeur de toutes les valeurs traduitent
    public function translate($value,$other=0,?string $lang=null):?array
    {
        $return = null;
        $other = $this->otherLang($other,$lang);

        if(!empty($other))
        {
            $keys = Base\Arrs::keys($this->arr($lang),$value);

            if(!empty($keys))
            $return = $this->takes($keys,$other);
        }

        return $return;
    }


    // numberFormat
    // retourne un format numérique lié à la classe base/number
    // si key est vide, retourne tout le tableau numberFormat
    // ne lance pas d'erreur si introuvable
    public function numberFormat(?string $key=null,?string $lang=null)
    {
        return $this->take(static::getPath('numberFormat',$key),$lang);
    }


    // numberPercentFormat
    // retourne un format numérique de pourcentage lié à la classe base/number
    // si key est vide, retourne tout le tableau numberFormat
    // ne lance pas d'erreur si introuvable
    public function numberPercentFormat(?string $key=null,?string $lang=null)
    {
        return $this->take(static::getPath('numberPercentFormat',$key),$lang);
    }


    // numberMoneyFormat
    // retourne un format monétaire lié à la classe base/number
    // si key est vide, retourne tout le tableau numberMoneyFormat
    // ne lance pas d'erreur si introuvable
    public function numberMoneyFormat(?string $key=null,?string $lang=null)
    {
        return $this->take(static::getPath('numberMoneyFormat',$key),$lang);
    }


    // numberPhoneFormat
    // retourne un format de taille lié à la classe base/number
    // si key est vide, retourne tout le tableau numberPhoneFormat
    // ne lance pas d'erreur si introuvable
    public function numberPhoneFormat(?string $key=null,?string $lang=null)
    {
        return $this->take(static::getPath('numberPhoneFormat',$key),$lang);
    }


    // numberSizeFormat
    // retourne un format de taille lié à la classe base/number
    // si key est vide, retourne tout le tableau numberSizeFormat
    // ne lance pas d'erreur si introuvable
    public function numberSizeFormat(?string $key=null,?string $lang=null)
    {
        return $this->take(static::getPath('numberSizeFormat',$key),$lang);
    }


    // dateMonth
    // retourne un nom de mois lié à la classe base/date à partir du tableau de langue
    // si key est vide, retourne tout le tableau dateMonth
    public function dateMonth(?int $key=null,?string $lang=null)
    {
        return $this->take(static::getPath('dateMonth',$key),$lang);
    }


    // dateFormat
    // retourne un format de date lié à la classe base/date
    // si key est vide, retourne tout le tableau dateFormat
    // ne lance pas d'erreur si introuvable
    public function dateFormat(?int $key=null,?string $lang=null)
    {
        return $this->take(static::getPath('dateFormat',$key),$lang);
    }


    // dateStr
    // retourne un text dateStr lié à la classe base/date à partir du tableau de langue
    // si key est vide, retourne tout le tableau dateStr
    public function dateStr(?string $key=null,?string $lang=null)
    {
        return $this->take(static::getPath('dateStr',$key),$lang);
    }


    // datePlaceholder
    // retourne un text dateStr lié à la classe base/date à partir du tableau de langue
    // si key est vide, retourne tout le tableau datePlaceholder
    public function datePlaceholder(?string $key=null,?string $lang=null)
    {
        return $this->take(static::getPath('datePlaceholder',$key),$lang);
    }


    // dateDay
    // retourne nom de jour lié à la classe base/date à partir du tableau de langue
    // si key est vide, retourne tout le tableau dateDay
    public function dateDay(?string $key=null,?string $lang=null)
    {
        return $this->take(static::getPath('dateDay',$key),$lang);
    }


    // dateDayShort
    // retourne un nom de jour court lié à la classe base/date à partir du tableau de langue
    // si key est vide, retourne tout le tableau dateDayShort
    public function dateDayShort(?string $key=null,?string $lang=null)
    {
        return $this->take(static::getPath('dateDayShort',$key),$lang);
    }


    // headerResponseStatus
    // retourne un nom statut de réponse lié à la classe base/header à partir du tableau de langue
    // si key est vide, retourne tout le tableau headerResponseStatus
    public function headerResponseStatus(?int $key=null,?string $lang=null)
    {
        return $this->take(static::getPath('headerResponseStatus',$key),$lang);
    }


    // errorCode
    // retourne un code d'erreur lié à la classe base/error
    // si key est vide, retourne tout le tableau errorCode
    // ne lance pas d'erreur si introuvable
    public function errorCode(?int $key=null,?string $lang=null)
    {
        return $this->take(static::getPath('errorCode',$key),$lang);
    }


    // errorLabel
    // retourne un nom d'erreur lié à la classe base/error
    // si key est vide, retourne tout le tableau errorLabel
    // ne lance pas d'erreur si introuvable
    public function errorLabel(?int $key=null,?string $lang=null)
    {
        return $this->take(static::getPath('errorLabel',$key),$lang);
    }


    // com
    // retourne un texte de communication, le type doit être spécifié
    // path peut être string ou array
    // utilise def, donc aucune erreur envoyé si inexistant
    public function com(string $type,$path,?array $replace=null,?string $lang=null,?array $option=null):?string
    {
        return $this->safe(static::getPath('com',[$type,$path]),$replace,$lang,$option);
    }


    // pos
    // retourne un texte de communication positif
    // path peut être string ou array
    // utilise def, donc aucune erreur envoyé si inexistant
    public function pos($path,?array $replace=null,?string $lang=null,?array $option=null):?string
    {
        return $this->def(static::getPath('pos',$path),$replace,$lang,$option);
    }


    // neg
    // retourne un texte de communication négatif
    // path peut être string ou array
    // utilise def, donc aucune erreur envoyé si inexistant
    public function neg($path,?array $replace=null,?string $lang=null,?array $option=null):?string
    {
        return $this->def(static::getPath('neg',$path),$replace,$lang,$option);
    }


    // getPath
    // retourne un chemin pour pour aller chercher un type de text dans un objet lang
    // retourne une string
    public static function getPath(string $type,$append=null):string
    {
        $return = '';

        if(array_key_exists($type,static::$config['path']))
        {
            $return = static::$config['path'][$type];

            if(is_array($return))
            $return = Base\Arrs::keyPrepare($return);

            if($append !== null)
            {
                if(is_array($append))
                $append = Base\Arrs::keyPrepare($append);

                $return = Base\Arrs::keyPrepare([$return,$append]);
            }
        }

        else
        static::throw('invalidPath');

        return $return;
    }
}
?>