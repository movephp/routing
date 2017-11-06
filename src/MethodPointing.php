<?php

namespace Movephp\Routing;

class MethodPointing
{

    /**
     * @var object
     */
    private $controller;

    /**
     * MethodPointing constructor.
     * @param object $controller
     */
    public function __construct($controller)
    {
        if (!is_object($controller)) {
            throw new \InvalidArgumentException(sprintf(
                '$controller must be an object, (%s) "%s" given',
                gettype($controller),
                print_r($controller, true)
            ));
        }
        $this->controller = $controller;
    }

    /**
     * @param string $methodName
     * @param array $arguments
     * @return callable
     */
    public function __call(string $methodName, array $arguments): callable
    {
        if (!empty($arguments)) {
            throw new \BadMethodCallException('Passing arguments is not allowed here');
        }
        if (!method_exists($this->controller, $methodName)) {
            throw new \BadMethodCallException(sprintf(
                'Method %s::%s() is not exists',
                get_class($this->controller),
                $methodName
            ));
        }
        return [$this->controller, $methodName];
    }
}
