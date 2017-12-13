<?php

declare(strict_types=1);

namespace Movephp\Routing;

use Psr\Http\Message\RequestInterface;

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

    /**
     * @return Route\RouteInterface[]
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * @param RequestInterface $request
     * @return Route\ResolvingInterface|null
     */
    public function resolve(RequestInterface $request): ?Route\ResolvingInterface
    {

    }

    /**
     * @param string $className
     * @param string $requiredInterface
     * @return bool
     * @throws Exception\InvalidClassException
     */
    public static function checkClassName(string $className, string $requiredInterface): bool
    {
        if (!class_exists($className)) {
            throw new Exception\InvalidClassException(sprintf(
                'Class "%s" in not exists, implementation of "%s" required',
                $className, $requiredInterface
            ));
        }
        if (!is_subclass_of($className, $requiredInterface)) {
            throw new Exception\InvalidClassException(sprintf(
                'Class "%s" does not implements "%s"',
                $className,
                $requiredInterface
            ));
        }
        if (!(new \ReflectionClass($className))->isInstantiable()) {
            throw new Exception\InvalidClassException(sprintf('Class "%s" is not instantiable', $className));
        }
        return true;
    }
}