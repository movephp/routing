<?php

namespace Movephp\Routing\Route\Parameter;

use Movephp\CallbackContainer;

class ParameterUntyped extends ParameterString
{
    /**
     * @return string
     */
    public static function matchPattern(): string
    {
        return '\{[_a-z][_a-z0-9]*\}';
    }

    /**
     * @param CallbackContainer\Parameter $actionParameter
     * @return bool
     */
    public function checkActionParameterType(CallbackContainer\Parameter $actionParameter): bool
    {
        return !$actionParameter->hasType() || in_array($actionParameter->type(), ['string', 'int', 'bool']);
    }
}