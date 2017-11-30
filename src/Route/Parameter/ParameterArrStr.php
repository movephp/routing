<?php

namespace Movephp\Routing\Route\Parameter;

class ParameterArrStr extends ParameterArr
{
    /**
     * @return string
     */
    public static function matchPattern(): string
    {
        return '\{[_a-z][_a-z0-9]*\|arr-str\}';
    }
}