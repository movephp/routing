<?php

declare(strict_types=1);

namespace Movephp\Routing\Route\Parameter;

use Movephp\CallbackContainer;

class ParameterEnum extends ParameterAbstract
{
    /**
     * @var string[]
     */
    private $variants = [];

    /**
     * @return string
     */
    public static function matchPattern(): string
    {
        return '\{[_a-z][_a-z0-9]*\|enum([a-z0-9-_\.:,]+)\}';
    }

    /**
     * ParameterAbstract constructor.
     * @param string $match
     */
    public function __construct(string $match)
    {
        parent::__construct($match);
        $this->variants = explode('|', trim($match, '{}'))[1];
        $this->variants = explode(',', $this->variants);
    }

    /**
     * @return string
     */
    public function urlPattern(): string
    {
        return '(' . implode('|', array_map('preg_quote', $this->variants)) . ')';
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