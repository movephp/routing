<?php

declare(strict_types=1);

namespace Movephp\Routing\Route\Parameter;

use Movephp\CallbackContainer;

class ParameterArrInt extends ParameterAbstract
{
    /**
     * @return string
     */
    public static function matchPattern(): string
    {
        return '\{[_a-z][_a-z0-9]*\|arr-int\}';
    }

    /**
     * @return string
     */
    public function urlPattern(): string
    {
        return '([0-9\/]+)';
    }

    /**
     * @param CallbackContainer\Parameter $actionParameter
     * @return bool
     */
    public function checkActionParameterType(CallbackContainer\Parameter $actionParameter): bool
    {
        return
            !$actionParameter->hasType() ||
            $actionParameter->type() === 'array' ||
            ($actionParameter->isVariadic() && $actionParameter->type() === 'int');
    }
}