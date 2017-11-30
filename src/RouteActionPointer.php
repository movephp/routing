<?php

namespace Movephp\Routing;

class RouteActionPointer
{
    /**
     * @var string
     */
    private $controllerClass;

    /**
     * RouteActionPointer constructor.
     * @param string $controllerClass
     */
    public function __construct(string $controllerClass)
    {
        // todo: check $controllerClass
        $this->controllerClass = $controllerClass;
    }

    /**
     * @param string $methodName
     * @param array $arguments
     * @return string[]
     * @throws \BadMethodCallException
     */
    public function __call(string $methodName, array $arguments): array
    {
        if (!empty($arguments)) {
            throw new \BadMethodCallException('Passing arguments is not allowed here');
        }
        // todo: check that method exists in the action class
        return [$this->controllerClass, $methodName];
    }
}
