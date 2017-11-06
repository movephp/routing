<?php

namespace Movephp\Routing;

class Router
{
    /**
     * @var string
     */
    private $routeBuilderClass = '';

    /**
     * @var string
     */
    private $routeClass = '';

    /**
     * @var RouteInterface[]
     */
    private $routes = [];

    /**
     * Router constructor.
     * @param string $routeBuilderClass
     * @param string $routeClass
     */
    public function __construct(string $routeBuilderClass = RouteBuilder::class, string $routeClass = Route::class)
    {
        $this->checkClass($routeBuilderClass, RouteBuilder::class);
        $this->routeBuilderClass = $routeBuilderClass;

        $this->checkClass($routeClass, RouteInterface::class);
        $this->routeClass = $routeClass;
    }

    /**
     * @param \string[] ...$patterns
     * @return RouteBuilder
     */
    public function onGet(string ...$patterns): RouteBuilder
    {
        return new $this->routeBuilderClass($this, ['GET'], $patterns);
    }

    /**
     * @param \string[] ...$patterns
     * @return RouteBuilder
     */
    public function onPost(string ...$patterns): RouteBuilder
    {
        return new $this->routeBuilderClass($this, ['POST'], $patterns);
    }

    /**
     * @param \string[] ...$patterns
     * @return RouteBuilder
     */
    public function onPut(string ...$patterns): RouteBuilder
    {
        return new $this->routeBuilderClass($this, ['PUT'], $patterns);
    }

    /**
     * @param \string[] ...$patterns
     * @return RouteBuilder
     */
    public function onDelete(string ...$patterns): RouteBuilder
    {
        return new $this->routeBuilderClass($this, ['DELETE'], $patterns);
    }

    /**
     * @param \string[] ...$patterns
     * @return RouteBuilder
     */
    public function onOptions(string ...$patterns): RouteBuilder
    {
        return new $this->routeBuilderClass($this, ['OPTIONS'], $patterns);
    }

    /**
     * @param \string[] ...$patterns
     * @return RouteBuilder
     */
    public function onAny(string ...$patterns): RouteBuilder
    {
        return new $this->routeBuilderClass($this, [], $patterns);
    }

    /**
     * @param string[] $methods
     * @param \string[] ...$patterns
     * @return RouteBuilder
     */
    public function onMatch(array $methods, string ...$patterns): RouteBuilder
    {
        return new $this->routeBuilderClass($this, $methods, $patterns);
    }

    /**
     * @param RouteInterface $route
     * @param \string[] ...$methods
     */
    public function add(RouteInterface $route, string ...$methods): void
    {
        $this->routes[] = $route;
    }

    /**
     * @return string
     */
    public function routeClass(): string
    {
        return $this->routeClass;
    }

    /**
     * @param string $className
     * @param string $parentName
     */
    private function checkClass(string $className, string $parentName): void
    {
        if (!class_exists($className)) {
            throw new \InvalidArgumentException(sprintf('Class "%s" in not exists', $className));
        }
        if ($className !== $parentName && !is_subclass_of($className, $parentName)) {
            throw new \InvalidArgumentException(sprintf(
                'Class "%s" is not subclass of "%s"',
                $className,
                $parentName
            ));
        }
        if (!(new \ReflectionClass($className))->isInstantiable()) {
            throw new \InvalidArgumentException(sprintf('Class "%s" is not instantiable', $className));
        }
    }
}