<?php

namespace Movephp\Routing\Route\Parameter;

use Movephp\CallbackContainer;

abstract class ParameterAbstract
{
    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var string
     */
    protected $match = '';

    /**
     * @return string
     */
    abstract public static function matchPattern(): string;

    /**
     * @return string
     */
    abstract public function urlPattern(): string;

    /**
     * ParameterAbstract constructor.
     * @param string $match
     */
    public function __construct(string $match)
    {
        $this->match = $match;
        $this->name = $this->getNameFromMatch($match);
    }

    /**
     * @param string $match
     * @return string
     */
    protected function getNameFromMatch(string $match): string
    {
        return explode('|', trim($match, '{}'))[0];
    }

    /**
     * @param CallbackContainer\Parameter $actionParameter
     * @return bool
     */
    abstract public function checkActionParameterType(CallbackContainer\Parameter $actionParameter): bool;

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function match(): string
    {
        return $this->match;
    }
}