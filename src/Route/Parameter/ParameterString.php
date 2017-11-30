<?php

declare(strict_types=1);

namespace Movephp\Routing\Route\Parameter;

use Movephp\CallbackContainer;

class ParameterString extends ParameterAbstract
{
    /**
     * @return string
     */
    public static function matchPattern(): string
    {
        return '\{[_a-z][_a-z0-9]*\|str\}';
    }

    /**
     * @return string
     */
    public function urlPattern(): string
    {
        return '([a-z0-9-_\.:]+)';
    }

    /**
     * @param CallbackContainer\Parameter $actionParameter
     * @return bool
     */
    public function checkActionParameterType(CallbackContainer\Parameter $actionParameter): bool
    {
        return !$actionParameter->hasType() || $actionParameter->type() === 'string';
    }
}