<?php

declare(strict_types=1);

namespace Movephp\Routing;

use Movephp\CallbackContainer\ContainerInterface as CallbackContainer;

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
     * @var string
     */
    private $resolvingClass = Route\Resolving::class;

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
     * @var null|Route\Resolving
     */
    private $resolving = null;

    /**
     * @var bool
     */
    private $isActionSet = false;

    /**
     * RouteBuilder constructor.
     * @param CallbackContainer $callbackFactory
     * @param string $routeClass
     * @param string $resolvingClass
     */
    public function __construct(
        CallbackContainer $callbackFactory,
        string $routeClass = '',
        string $resolvingClass = ''
    ) {
        $this->callbackFactory = $callbackFactory;
        if ($routeClass && $this->checkClassName($routeClass, Route\RouteInterface::class)) {
            $this->routeClass = $routeClass;
        }
        if ($resolvingClass && $this->checkClassName($resolvingClass, Route\ResolvingInterface::class)) {
            $this->resolvingClass = $resolvingClass;
        }
    }

    /**
     *
     */
    public function __clone()
    {
        $this->httpMethods = [];
        $this->patterns = [];
        $this->resolving = null;
        $this->isActionSet = false;
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
     * @param callable|array $filter
     * @return RouteBuilder
     */
    public function filter($filter): self
    {
        $this->getResolving()->addFilterBefore(
            $this->callbackFactory->make($filter)
        );
        return $this;
    }

    /**
     * @param callable|array $filter
     * @return RouteBuilder
     */
    public function out($filter): self
    {
        $this->getResolving()->addFilterAfter(
            $this->callbackFactory->make($filter)
        );
        return $this;
    }

    /**
     * @param callable|array $action
     * @return RouteBuilder
     * @throws Exception\PatternNotSetException
     * @throws Exception\RouterNotSetException
     * @throws \BadMethodCallException
     */
    public function call($action): self
    {
        if ($this->isActionSet) {
            throw new \BadMethodCallException(sprintf(
                'Only one call to the %s() method in a fluent interface of %s is allowed',
                __METHOD__, __CLASS__
            ));
        }
        if (!$this->router) {
            throw new Exception\RouterNotSetException(sprintf(
                'Its required to set instance of %s with method %s::setRouter() before calling %s()',
                Router::class, __CLASS__, __METHOD__
            ));
        }
        if (empty($this->patterns)) {
            throw new Exception\PatternNotSetException(sprintf(
                'Its required to set at least one route pattern with method %s::init() before calling %s()',
                Router::class, __CLASS__, __METHOD__
            ));
        }
        $httpMethods = $this->httpMethods;
        if (empty($httpMethods)) {
            $httpMethods = [''];
        }
        $this->getResolving()->setAction(
            $this->callbackFactory->make($action)
        );
        foreach ($this->patterns as $pattern) {
            foreach ($httpMethods as $httpMethod) {
                /**
                 * @var Route\RouteInterface $route
                 */
                $route = new $this->routeClass(
                    $httpMethod,
                    $pattern,
                    $this->getResolving()
                );
                $this->router->add($route);
                $this->isActionSet = true;
            }
        }
        return $this;
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
        $this->httpMethods = $httpMethods;
    }

    /**
     * @param string[] $patterns
     * @throws Exception\PatternNotSetException
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
            throw new Exception\PatternNotSetException('$patterns must be an non-empty array');
        }
        $this->patterns = $patterns;
    }

    /**
     * @return Route\ResolvingInterface
     */
    private function getResolving(): Route\ResolvingInterface
    {
        if (!$this->resolving) {
            $this->resolving = new $this->resolvingClass();
        }
        return $this->resolving;
    }

    /**
     * @param string $className
     * @param string $requiredInterface
     * @return bool
     * @throws Exception\InvalidClassException
     */
    private function checkClassName(string $className, string $requiredInterface): bool
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