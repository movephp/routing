<?php

declare(strict_types=1);

namespace Movephp\Routing\Route;

interface RouteInterface
{
    /**
     * RouteInterface constructor.
     * @param string $httpMethod    Empty string should match any http-method
     * @param string $pattern
     * @param ResolvingInterface $resolving
     */
    public function __construct(string $httpMethod, string $pattern, ResolvingInterface $resolving);

    /**
     * @return bool
     */
    public function isSerializable(): bool;
}