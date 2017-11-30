<?php

namespace Movephp\Routing;

class Router
{
    /**
     * @var RouteBuilder
     */
    private $routeBuilder;

    /**
     * @var Route\RouteInterface[]
     */
    private $routes = [];

    /**
     * Router constructor.
     * @param RouteBuilder $routeBuilder
     */
    public function __construct(
        RouteBuilder $routeBuilder
    ) {
        $this->routeBuilder = $routeBuilder;
        $this->routeBuilder->setRouter($this);
    }

    /**
     * @param string[] $methods
     * @param \string[] ...$patterns
     * @return RouteBuilder
     */
    public function onMatch(array $methods, string ...$patterns): RouteBuilder
    {
        $builder = clone($this->routeBuilder);
        $builder->init($methods, $patterns);
        return $builder;
    }

    /**
     * @param \string[] ...$patterns
     * @return RouteBuilder
     */
    public function onGet(string ...$patterns): RouteBuilder
    {
        return $this->onMatch(['GET'], ...$patterns);
    }

    /**
     * @param \string[] ...$patterns
     * @return RouteBuilder
     */
    public function onPost(string ...$patterns): RouteBuilder
    {
        return $this->onMatch(['POST'], ...$patterns);
    }

    /**
     * @param \string[] ...$patterns
     * @return RouteBuilder
     */
    public function onPut(string ...$patterns): RouteBuilder
    {
        return $this->onMatch(['PUT'], ...$patterns);
    }

    /**
     * @param \string[] ...$patterns
     * @return RouteBuilder
     */
    public function onDelete(string ...$patterns): RouteBuilder
    {
        return $this->onMatch(['DELETE'], ...$patterns);
    }

    /**
     * @param \string[] ...$patterns
     * @return RouteBuilder
     */
    public function onOptions(string ...$patterns): RouteBuilder
    {
        return $this->onMatch(['OPTIONS'], ...$patterns);
    }

    /**
     * @param \string[] ...$patterns
     * @return RouteBuilder
     */
    public function onAny(string ...$patterns): RouteBuilder
    {
        return $this->onMatch([], ...$patterns);
    }

    /**
     * @param Route\RouteInterface $route
     */
    public function add(Route\RouteInterface $route): void
    {
        $this->routes[] = $route;
    }
}