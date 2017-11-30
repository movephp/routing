<?php

namespace Movephp\Routing;

use Movephp\CallbackContainer\Container as CallbackContainer;

class RouteBuilder
{
    /**
     * @var CallbackContainer
     */
    private $callbackFactory;

    /**
     * @var string
     */
    private $routeClass = Route\Route::class;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var string[]
     */
    private $httpMethods = [];

    /**
     * @var string[]
     */
    private $patterns = [];

    /**
     * @var null|callable|array
     */
    private $authorization = null;

    /**
     * RouteBuilder constructor.
     * @param CallbackContainer $callbackFactory
     * @param string $routeClass
     * @throws \InvalidArgumentException
     */
    public function __construct(CallbackContainer $callbackFactory, string $routeClass = '')
    {
        $this->callbackFactory = $callbackFactory;
        if($routeClass){
            if (!class_exists($routeClass)) {
                throw new \InvalidArgumentException(sprintf('Class "%s" in not exists', $routeClass));
            }
            if(!is_subclass_of($routeClass, Route\RouteInterface::class)) {
                throw new \InvalidArgumentException(sprintf(
                    'Class "%s" does not implements "%s"',
                    $routeClass,
                    Route\RouteInterface::class
                ));
            }
            if (!(new \ReflectionClass($routeClass))->isInstantiable()) {
                throw new \InvalidArgumentException(sprintf('Class "%s" is not instantiable', $routeClass));
            }
            $this->routeClass = $routeClass;
        }
    }

    /**
     *
     */
    public function __clone()
    {
        $this->patterns = [];
        $this->authorization = null;
    }

    /**
     * @param Router $router
     */
    public function setRouter(Router $router): void
    {
        $this->router = $router;
    }

    /**
     * @param string[] $httpMethods
     * @param string[] $patterns
     */
    public function init(array $httpMethods, array $patterns): void
    {
        $this->setHttpMethods($httpMethods);
        $this->setPatterns($patterns);
    }

    /**
     * @param callable|array $authorization
     * @return RouteBuilder
     */
    public function when($authorization): self
    {
        $this->authorization = $authorization;
        return $this;
    }

    /**
     * @param callable|array $action
     */
    public function call($action): void
    {
        $actionCallback = $this->callbackFactory->make($action);
        $authorizationCallback = $this->authorization ? $this->callbackFactory->make($this->authorization) : null;
        foreach ($this->patterns as $pattern) {
            foreach ($this->httpMethods as $httpMethod) {
                $route = new $this->routeClass(
                    $httpMethod,
                    $pattern,
                    $actionCallback,
                    $authorizationCallback
                );
                $this->router->add($route);
            }
        }
    }

    /**
     * @param string[] $httpMethods
     * @throws \InvalidArgumentException
     */
    private function setHttpMethods(array $httpMethods): void
    {
        foreach ($httpMethods as $method) {
            if (!is_string($method)) {
                throw new \InvalidArgumentException(sprintf(
                    '$httpMethods must be an array of strings, given: %s',
                    print_r($httpMethods, true)
                ));
            }
        }
        if (empty($httpMethods)) {
            $httpMethods = [''];
        }
        $this->httpMethods = $httpMethods;
    }

    /**
     * @param string[] $patterns
     * @throws \InvalidArgumentException
     */
    private function setPatterns(array $patterns): void
    {
        foreach ($patterns as $pattern) {
            if (!is_string($pattern)) {
                throw new \InvalidArgumentException(sprintf(
                    '$typePatterns must be an array of strings, "%s" given',
                    print_r($patterns, true)
                ));
            }
        }
        if (empty($patterns)) {
            throw new \InvalidArgumentException('$typePatterns must be an non-empty array');
        }
        $this->patterns = $patterns;
    }
}