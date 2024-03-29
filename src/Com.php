<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main;
use Quid\Base;
use Quid\Base\Html;

// com
// class that provides the logic to store positive, negative or neutral communication messages
class Com extends Map
{
    // config
    protected static array $config = [
        'default'=>'neg', // type par défaut
        'all'=>['neg','pos','neutral'] // tous les types
    ];


    // dynamique
    protected ?array $mapAllow = ['push','unshift','unset','empty','splice','index','serialize','jsonSerialize','clone']; // méthodes permises
    protected array $type = []; // conserve les types acceptés par l'objet (comme pos, neg)
    protected $mapIs = true; // les valeurs doivent passés la méthode is


    // construct
    // construit l'objet de communication
    final public function __construct(?array $value=null,?array $attr=null)
    {
        $this->makeAttr($attr);
        $this->setType($value);
    }


    // toString
    // output de com
    final public function __toString():string
    {
        return $this->output();
    }


    // onPrepareValue
    // prépare une valeur pour une méthode comme in, keys et search
    // support pour retrouver une valeur du tableau de communication via un tableau a deux index (type,key)
    final protected function onPrepareValue($return)
    {
        if(is_array($return) && count($return) === 2)
        $return = $this->findCommon($return,$this->arr());

        return $return;
    }


    // cast
    // cast l'objet, output com
    final public function _cast():string
    {
        return $this->output();
    }


    // is
    // méthode de validation de map pour push et unshift
    final public function is($value):bool
    {
        $return = false;

        if(is_array($value) && count($value) >= 2)
        {
            $type = current($value);
            if($this->isType($type))
            {
                $path = Base\Arr::get(1,$value);
                $return = (is_string($path) && !empty($path));
            }
        }

        return $return;
    }


    // isType
    // retourne vrai si le type est supporté par l'objet
    final public function isType($value):bool
    {
        return is_string($value) && in_array($value,$this->type,true);
    }


    // checkType
    // envoie une exception si le type n'est pas supporté par l'objet
    final public function checkType($value):self
    {
        if(!$this->isType($value))
        static::throw();

        return $this;
    }


    // type
    // retourne le type ou le type par défaut si null
    final public function type(?string $type=null):string
    {
        return $type ?? $this->getAttr('default');
    }


    // getType
    // retourne le tableau des types
    final public function getType():array
    {
        return $this->type;
    }


    // setType
    // remplace les types et vide le tableau
    // si value est null, prend les types par défaut dans config
    final protected function setType(?array $value=null):void
    {
        if($value === null)
        $value = $this->getAttr('all');

        if(!is_array($value) || !Base\Arr::validate('string',$value))
        static::throw();

        $this->data = [];
        $this->type = array_values($value);
    }


    // lang
    // retourne l'objet lang
    // envoie une exception si introuvable
    protected function lang(?Lang $return=null):Lang
    {
        return $return ?: static::throw();
    }


    // payload
    // prépare le payload en vue d'un ajout
    // si type est null, utilise le type par défaut
    // exception envoyé si le tableau de retour est vide, très strict
    final public function payload(?string $type,$path,?array $replace=null,$attr=null,array ...$ins):array
    {
        $return = null;
        $type = $this->type($type);
        $this->checkType($type);
        $path = Base\Obj::cast($path);

        if(is_array($path))
        $path = Base\Arrs::keyPrepare($path);

        if(is_string($path) && !empty($path))
        {
            $return = [$type,$path,$replace,$attr];
            $return = Base\Obj::cast($return);

            if(!empty($ins))
            {
                $deep = [];

                foreach ($ins as $in)
                {
                    if(!empty($in))
                    {
                        if(count($in) < 2)
                        static::throw('typeAndPath','requiredForDeep');

                        $value = $this->payload(...$in);
                        if(!empty($value))
                        $deep[] = $value;
                    }
                }

                if(!empty($deep))
                $return[] = $deep;
            }
        }

        return $return ?: static::throw($type,$path);
    }


    // findCommon
    // méthode utilisé par onPrepareValue et update pour trouver un élément de communication commun
    final public function findCommon(array $return,array $data)
    {
        $return = array_values($return);
        $value = $this->payload(...$return);

        if(is_array($value) && !empty($value))
        {
            foreach ($data as $v)
            {
                if($value[0] === $v[0] && $value[1] === $v[1])
                {
                    $return = $v;
                    break;
                }
            }
        }

        return $return;
    }


    // update
    // met à jour un élément de communication dans le tableau
    // la méthode est récursive
    // replace et attr sont merge
    // in est unshift ou push
    final protected function update(string $method,array $value,array $return):array
    {
        if(!in_array($method,['unshift','push'],true))
        static::throw();

        if(array_key_exists(2,$value) && is_array($value[2]) && !empty($value[2]))
        $return[2] = Base\Arr::replace($return[2] ?? [],$value[2]);

        if(array_key_exists(3,$value) && !empty($value[3]))
        $return[3] = Base\Attr::append($return[3] ?? null,$value[3]);

        if(array_key_exists(4,$value) && is_array($value[4]) && !empty($value[4]))
        {
            $return[4] = (!empty($return[4]))? $return[4]:[];

            foreach ($value[4] as $z)
            {
                if(is_array($z) && array_key_exists(0,$z) && array_key_exists(1,$z) && !empty($return[4]))
                {
                    $find = [$z[0],$z[1]];
                    $val = $this->findCommon($find,$return[4]);

                    if(!empty($val))
                    {
                        $k = array_search($val,$return[4],true);

                        if(is_int($k))
                        {
                            $return[4][$k] = $this->update($method,$z,$return[4][$k]);
                            unset($z);
                        }
                    }
                }

                if(isset($z))
                $return[4] = Base\Arr::$method($return[4],$z);
            }
        }

        return $return;
    }


    // unshift
    // ajoute une valeur au début du tableau
    // bloque les doublons
    final public function unshift(...$values):self
    {
        $data =& $this->arr();

        foreach ($values as $key => $value)
        {
            if(is_array($value) && array_key_exists(0,$value) && array_key_exists(1,$value))
            {
                $find = [$value[0],$value[1]];
                $k = $this->search($find);
                if(is_int($k))
                {
                    $data[$k] = $this->update('unshift',$value,$data[$k]);
                    unset($values[$key]);
                }
            }
        }

        if(!empty($values))
        parent::unshift(...$values);

        return $this;
    }


    // push
    // ajoute une valeur à la fin du tableau
    // bloque les doublons
    final public function push(...$values):self
    {
        $data =& $this->arr();

        foreach ($values as $key => $value)
        {
            if(is_array($value) && array_key_exists(0,$value) && array_key_exists(1,$value))
            {
                $find = [$value[0],$value[1]];
                $k = $this->search($find);
                if(is_int($k))
                {
                    $data[$k] = $this->update('push',$value,$data[$k]);
                    unset($values[$key]);
                }
            }
        }

        if(!empty($values))
        parent::push(...$values);

        return $this;
    }


    // prepend
    // prepend une nouvelle entrée au tableau de communication
    final public function prepend(?string $type,$path,?array $replace=null,$attr=null,array ...$ins):self
    {
        return $this->unshift($this->payload($type,$path,$replace,$attr,...$ins));
    }


    // append
    // ajoute une nouvelle entrée au tableau de communication
    final public function append(?string $type,$path,?array $replace=null,$attr=null,array ...$ins):self
    {
        return $this->push($this->payload($type,$path,$replace,$attr,...$ins));
    }


    // pos
    // ajoute un élément de type positif
    final public function pos($path,?array $replace=null,$attr=null,array ...$ins):self
    {
        return $this->append('pos',$path,$replace,$attr,...$ins);
    }


    // posPrepend
    // prepend un élément de type positif
    final public function posPrepend($path,?array $replace=null,$attr=null,array ...$ins):self
    {
        return $this->prepend('pos',$path,$replace,$attr,...$ins);
    }


    // neg
    // ajoute un élément de type négatif
    final public function neg($path,?array $replace=null,$attr=null,array ...$ins):self
    {
        return $this->append('neg',$path,$replace,$attr,...$ins);
    }


    // negPrepend
    // prepend un élément de type négatif
    final public function negPrepend($path,?array $replace=null,$attr=null,array ...$ins):self
    {
        return $this->prepend('neg',$path,$replace,$attr,...$ins);
    }


    // neutral
    // ajoute un élément de type neutre
    final public function neutral($path,?array $replace=null,$attr=null,array ...$ins):self
    {
        return $this->append('neutral',$path,$replace,$attr,...$ins);
    }


    // neutralPrepend
    // prepend un élément de type neutre
    final public function neutralPrepend($path,?array $replace=null,$attr=null,array ...$ins):self
    {
        return $this->prepend('neutral',$path,$replace,$attr,...$ins);
    }


    // posNeg
    // ajoute un élément positif et/ou négatif
    // les valeurs nulls sont ignorés
    final public function posNeg($pos=null,$neg=null,?array $replace=null,$attr=null,array ...$ins):self
    {
        if($pos !== null)
        $this->pos($pos,$replace,$attr,...$ins);

        if($neg !== null)
        $this->neg($neg,$replace,$attr,...$ins);

        return $this;
    }


    // posNegPrepend
    // prepend un élément positif et/ou négatif
    // les valeurs nulls sont ignorés
    final public function posNegPrepend($pos=null,$neg=null,?array $replace=null,$attr=null,array ...$ins):self
    {
        if($pos !== null)
        $this->posPrepend($pos,$replace,$attr,...$ins);

        if($neg !== null)
        $this->negPrepend($neg,$replace,$attr,...$ins);

        return $this;
    }


    // posNegLogStrict
    // méthode utilisé par différentes classes pour faire de la com pos, neg
    // pos ou neg doit avoir une valeur non null, sinon skip
    // loggé et/ou envoyé une exception s'il y a des messages négatifs
    final public function posNegLogStrict(string $type,bool $bool,$pos,$neg,?string $log=null,?array $option=null):self
    {
        if(strlen($type) && ($pos !== null || $neg !== null))
        {
            $option = Base\Arr::plus(['com'=>false,'log'=>true,'strict'=>false],$option);

            if($option['com'] === true)
            $this->posNeg($pos,$neg);

            if($option['log'] === true && !empty($log))
            $log::logCloseDown($type,['key'=>$type,'bool'=>$bool,'pos'=>$pos,'neg'=>$neg]);

            if($option['strict'] === true && !empty($neg))
            static::throw($type,$neg);
        }

        return $this;
    }


    // depth
    // retourne la profondeur de l'objet de communication
    final public function depth():int
    {
        return Base\Arrs::depth($this->arr());
    }


    // stripFloor
    // enlève le premier niveau du tableau de communication
    // les données dans les étages plus profond sont conservés
    final public function stripFloor():self
    {
        $data =& $this->arr();
        $keep = [];

        foreach ($data as $key => $value)
        {
            if(is_array($value) && array_key_exists(4,$value))
            $keep = Base\Arr::merge($keep,$value[4]);
        }

        if(!empty($keep))
        $data = $keep;

        return $this;
    }


    // keepFloor
    // conserve le premier niveau du tableau de communication
    // les données dans les étages plus profond sont effacés
    final public function keepFloor():self
    {
        $data =& $this->arr();

        foreach ($data as $key => $value)
        {
            if(is_array($value) && array_key_exists(4,$value))
            unset($data[$key][4]);
        }

        return $this;
    }


    // keepCeiling
    // permet de garder seulement le niveau de communication le plus élevé
    // les données dans les étages inférieurs sont effacés
    final public function keepCeiling():self
    {
        $depth = $this->depth();

        if(is_int($depth))
        {
            $depth -= 2;

            if($depth >= 2)
            {
                $count = ($depth / 2);
                for ($i=0; $i < $count; $i++)
                {
                    $this->stripFloor();
                }
            }
        }

        return $this;
    }


    // keepFirst
    // garde seulement la première entrée de l'objet com
    final public function keepFirst():self
    {
        $first = $this->first();
        $data =& $this->arr();
        $data = [$first];

        return $this;
    }


    // keepLast
    // garde seulement la dernière entrée de l'objet com
    final public function keepLast():self
    {
        $last = $this->last();
        $data =& $this->arr();
        $data = [$last];

        return $this;
    }


    // stripType
    // enlève les éléments de communication de premier niveau ayant le type donnée en argument
    final public function stripType(?string $type=null):self
    {
        $type = $this->type($type);
        $data =& $this->arr();

        foreach ($data as $key => $value)
        {
            if(!empty($value[0]) && $value[0] === $type)
            unset($data[$key]);
        }

        return $this;
    }


    // keepType
    // garde les éléments de communication de premier niveau ayant le type donnée en argument
    final public function keepType(?string $type=null):self
    {
        $type = $this->type($type);
        $data =& $this->arr();

        foreach ($data as $key => $value)
        {
            if(!empty($value[0]) && $value[0] !== $type)
            unset($data[$key]);
        }

        return $this;
    }


    // prepareIn
    // méthode utilisé pour générer le tableau de communication pour une structure complexe à multiple niveaux
    // n'ajoute pas à l'objet
    final public function prepareIn(string $type,?string $inType,array $array,?array $replace=null):array
    {
        $return = [];
        $this->checkType($type);
        $inType = $this->type($inType);

        foreach ($array as $key => $value)
        {
            $in = null;

            if(is_string($key))
            {
                if(is_string($value))
                $value = [$value];

                if(is_array($value))
                {
                    $in = [$type,$key,$replace,null];

                    foreach ($value as $v)
                    {
                        $in[] = [$inType,$v,$replace,null];
                    }
                }
            }

            if(!empty($in))
            $return[] = $in;
        }

        return $return;
    }


    // prepare
    // prépare un élément du tableau
    // retourne null ou un tableau avec deux ou trois index, le premier est le type et le deuxième le texte
    // le troisième index serait in ou le tableau a creusé
    final protected function prepare(array $value,Lang $obj,?string $lang=null,?array $option=null):?array
    {
        $return = null;

        if(static::is($value))
        {
            $return = [];
            $type = current($value);
            $this->checkType($type);
            $path = (array_key_exists(1,$value))? $value[1]:null;
            $replace = (array_key_exists(2,$value))? $value[2]:null;
            $attr = (array_key_exists(3,$value))? $value[3]:null;
            $in = (array_key_exists(4,$value))? $value[4]:null;

            if(!empty($type) && !empty($path))
            {
                $text = $obj->com($type,$path,$replace,$lang,$option);
                if(empty($text) && is_string($path))
                $text = $path;

                if(!empty($text))
                {
                    $return[] = $type;
                    $return[] = $text;
                    $return[] = $attr;

                    if(!empty($in))
                    $return[] = $in;
                }
            }
        }

        return $return;
    }


    // output
    // fait ou output en html de tout le contenu de objet com
    // l'objet lang peut être fourni, possible de changer la lang de sortie et les options
    final public function output(?Lang $obj=null,?string $lang=null,?array $option=null):string
    {
        return $this->makeOutput(null,$this->arr(),$obj,$lang,$option);
    }


    // outputNeg
    // fait un output en html des contenus négatifs de l'objet com
    final public function outputNeg(?Lang $obj=null,?string $lang=null,?array $option=null):string
    {
        return $this->makeOutput('neg',$this->arr(),$obj,$lang,$option);
    }


    // outputPos
    // fait un output en html des contenus positifs de l'objet com
    final public function outputPos(?Lang $obj=null,?string $lang=null,?array $option=null):string
    {
        return $this->makeOutput('pos',$this->arr(),$obj,$lang,$option);
    }


    // outputNeutral
    // fait un output en html des contenus neutres de l'objet com
    final public function outputNeutral(?Lang $obj=null,?string $lang=null,?array $option=null):string
    {
        return $this->makeOutput('neutral',$this->arr(),$obj,$lang,$option);
    }


    // makeOutput
    // fait un output en html des messages avec types fournis en arguments
    // si types est null, tous les types sont choisis
    // l'objet lang peut être fourni, possible de changer la lang de sortie et les options
    // si l'objet lang n'est pas fourni, va chercher celu dans inst
    // s'il faut creuser le tableau, la valeur types devient null afin d'obtenir les messages de tous les types en creusant
    // des exceptions peuvent être envoyés
    final protected function makeOutput($types,array $data,?Lang $obj=null,?string $lang=null,?array $option=null):string
    {
        $return = '';

        if($types === null)
        $types = $this->getType();

        if(is_string($types))
        $types = [$types];

        if(empty($types) || !is_array($types))
        static::throw('invalidTypes');

        if(!empty($data))
        {
            $obj = $this->lang($obj);

            foreach ($data as $value)
            {
                $value = $this->prepare($value,$obj,$lang,$option);

                if(!empty($value) && in_array($value[0],$types,true))
                {
                    $attr = Base\Attr::append($value[0],$value[2]);
                    $html = Html::span($value[1]);

                    if(!empty($value[3]) && is_array($value[3]))
                    $html .= $this->makeOutput(null,$value[3],$obj,$lang,$option);

                    $return .= Html::li($html,$attr);
                }
            }
        }

        return Html::ulCond($return);
    }


    // flush
    // fait ou output en html de tout le contenu de objet com et ensuite vide l'objet
    final public function flush(?Lang $obj=null,?string $lang=null,?array $option=null):string
    {
        return $this->makeFlush(null,$this->arr(),$obj,$lang,$option);
    }


    // flushNeg
    // fait un output en html des contenus négatifs de l'objet com et ensuite vide l'objet entièrement
    // les messages des autres types sont aussi effacés
    final public function flushNeg(?Lang $obj=null,?string $lang=null,?array $option=null):string
    {
        return $this->makeFlush('neg',$this->arr(),$obj,$lang,$option);
    }


    // flushPos
    // fait un output en html des contenus positifs de l'objet com et ensuite vide l'objet entièrement
    // les messages des autres types sont aussi effacés
    final public function flushPos(?Lang $obj=null,?string $lang=null,?array $option=null):string
    {
        return $this->makeFlush('pos',$this->arr(),$obj,$lang,$option);
    }


    // flushNeutral
    // fait un output en html des contenus neutres de l'objet com et ensuite vide l'objet entièrement
    // les messages des autres types sont aussi effacés
    final public function flushNeutral(?Lang $obj=null,?string $lang=null,?array $option=null):string
    {
        return $this->makeFlush('neutral',$this->arr(),$obj,$lang,$option);
    }


    // makeFlush
    // fait un output en html des messages avec types fournis en arguments et ensuite vide l'objet entièrement
    // les messages des autres types sont aussi effacés
    final protected function makeFlush($types,array $data,?Lang $obj=null,?string $lang=null,?array $option=null):string
    {
        $return = $this->makeOutput($types,$data,$obj,$lang,$option);
        $this->empty();

        return $return;
    }


    // error
    // ajoute un message à partir d'un objet d'error
    final public function error(Error $value):self
    {
        return $this->neg($value);
    }
}
?>