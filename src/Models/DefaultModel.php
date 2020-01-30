<?php namespace NFse\Models;

use Exception;

abstract class DefaultModel
{
    public function __set($name, $value)
    {
        throw new Exception(sprintf('Classe "%s" não possui propriedade "%s"', get_class($this), $name));
    }
}
