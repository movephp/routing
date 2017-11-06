<?php

namespace Movephp\Routing;

class RouteBuilder
{
    /**
     * @var Router
     */
    private $router;

    /**
     * @var string[]
     */
    private $methods = [];

    /**
     * @var string[]
     */
    private $patterns = [];

    /**
     * @var null|callable
     */
    private $authorization = null;

    /**
     * RouteBuilder constructor.
     * @param Router $router
     * @param string[] $methods
     * @param string[] $patterns
     */
    public function __construct(Router $router, array $methods, array $patterns)
    {
        $this->router = $router;

        foreach ($methods as $method) {
            if (!is_string($method)) {
                throw new \InvalidArgumentException(sprintf(
                    '$methods must be an array of strings, "%s" given',
                    print_r($methods, true)
                ));
            }
        }
        if (empty($methods)) {
            $methods = [''];
        }
        $this->methods = $methods;

        foreach ($patterns as $pattern) {
            if (!is_string($pattern)) {
                throw new \InvalidArgumentException(sprintf(
                    '$patterns must be an array of strings, "%s" given',
                    print_r($patterns, true)
                ));
            }
        }
        if(empty($patterns)){
            throw new \InvalidArgumentException('$patterns must be an non-empty array of strings');
        }
        $this->patterns = $patterns;
    }

    /**
     * @param callable $authorization
     * @return RouteBuilder
     */
    public function when(callable $authorization): self
    {
        $this->authorization = $authorization;
        return $this;
    }

    /**
     * @param callable $controller
     */
    public function call(callable $controller): void
    {
        $routeClass = $this->router->routeClass();
        foreach ($this->patterns as $pattern) {
            foreach ($this->methods as $method) {
                $route = new $routeClass(
                    $method,
                    $pattern,
                    $controller,
                    $this->authorization
                );
                $this->router->add($route);
            }
        }
    }
}