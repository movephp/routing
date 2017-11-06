<?php

namespace Movephp\Routing;

class Route implements RouteInterface
{
    /**
     * @var string
     */
    private $method = '';

    /**
     * @var string
     */
    private $pattern = '';

    /**
     * @var \Closure
     */
    private $controller;

    /**
     * @var callable|null
     */
    private $authorization = null;

    /**
     * Route constructor.
     * @param string $method
     * @param string $pattern
     * @param callable $controller
     * @param callable|null $authorization
     */
    public function __construct(string $method, string $pattern, callable $controller, callable $authorization = null)
    {
        if (!$controller instanceof \Closure) {
            $controller = \Closure::fromCallable($controller);
        }
        $this->method = $method;
        $this->pattern = $pattern;
        $this->controller = $controller;
        $this->authorization = $authorization;
    }


    /*public function test()
    {
        $rf = new \ReflectionFunction($this->controller);
        $test = [];
        foreach ($rf->getParameters() as $parameter) {
            $test[$parameter->getName()] = (string)$parameter->getType();
        }
        return $test;
    }*/
}