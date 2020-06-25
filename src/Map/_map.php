<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package <https://quidphp.com>
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quidphp/main/blob/master/LICENSE
 */

namespace Quid\Main\Map;
use Quid\Main;

// _map
// trait that provides a method to recursively change the entries (map) by providing a callback
trait _map
{
    // map
    // permet d'utiliser une closure pour changer les valeurs de l'objet
    // la nouvelle valeur est passé dans la méthode set
    // la clé est envoyé en deuxième argument
    final public function map(\Closure $closure):Main\Map
    {
        $this->checkAllowed('map');
        $return = $this->onPrepareThis('map');

        foreach ($this->arr() as $key => $value)
        {
            $new = $closure($value,$key);

            if($new !== $value)
            $return->set($key,$new);
        }

        return $return;
    }
}
?>