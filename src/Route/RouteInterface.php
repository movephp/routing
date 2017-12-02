<?php

declare(strict_types=1);

namespace Movephp\Routing\Route;

use Movephp\CallbackContainer\Container as CallbackContainer;

interface RouteInterface
{
    /**
     * RouteInterface constructor.
     * @param string $httpMethod    Empty string should match any http-method
     * @param string $pattern
     * @param CallbackContainer $action
     * @param CallbackContainer|null $authorization
     */
    public function __construct(string $httpMethod, string $pattern, CallbackContainer $action, CallbackContainer $authorization = null);

    /**
     * @return bool
     */
    public function isSerializable(): bool;
}