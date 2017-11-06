<?php

namespace Movephp\Routing;

interface RouteInterface
{
    /**
     * RouteInterface constructor.
     * @param string $method    Empty string should match any http-methods
     * @param string $pattern
     * @param callable $controller
     * @param callable|null $authorization
     */
    public function __construct(string $method, string $pattern, callable $controller, callable $authorization = null);
}