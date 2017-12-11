<?php

declare(strict_types=1);

namespace Movephp\Routing\Route;

class Route implements RouteInterface
{
    /**
     * @var string
     */
    private $httpMethod = '';

    /**
     * @var string
     */
    private $patternOrig = '';

    /**
     * @var string
     */
    private $patternRegexp = '';

    /**
     * @var Parameter\ParameterAbstract[]
     */
    private $parameters = [];

    /**
     * @var ResolvingInterface
     */
    private $resolving;

    /**
     * Route constructor.
     * @param string $httpMethod
     * @param string $pattern
     * @param ResolvingInterface $resolving
     */
    public function __construct(
        string $httpMethod,
        string $pattern,
        ResolvingInterface $resolving
    ) {
        $this->httpMethod = $httpMethod;
        $this->resolving = $resolving;
        $this->setPatternAndParameters($pattern);
        $this->checkParameters();
    }

    /**
     * @return bool
     */
    public function isSerializable(): bool
    {
        return $this->resolving->isSerializable();
    }

    /**
     * @return ResolvingInterface
     */
    public function getResolving(): ResolvingInterface
    {
        return $this->resolving;
    }

    /**
     * @param string $pattern
     */
    private function setPatternAndParameters(string $pattern): void
    {
        $this->patternOrig = $pattern;
        $this->parameters = Parameter\Factory::makeFromPattern($pattern);

        $pattern = rtrim($pattern, '/');
        $pattern = str_replace('/', '\\/', $pattern);

        foreach ($this->parameters as $parameter) {
            $pattern = str_replace($parameter->match(), $parameter->urlPattern(), $pattern);
        }
        $this->patternRegexp = '/^' . $pattern . '((\\/?)|(\\/\\?.*))$/ius';
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function checkParameters(): void
    {
        /**
         * @var \Movephp\CallbackContainer\Parameter[] $actionParameters
         */
        $actionParameters = [];
        foreach ($this->resolving->getAction()->parameters() as $parameter) {
            $actionParameters[$parameter->name()] = $parameter;
        }

        /**
         * @var Parameter\ParameterAbstract[] $routeParameters
         */
        $routeParameters = [];
        foreach ($this->parameters as $parameter) {
            $routeParameters[$parameter->name()] = $parameter;
        }

        // Check that the required parameters of the action are present in the routing template
        foreach ($actionParameters as $actionParameter) {
            if ($actionParameter->isOptional()) {
                continue;
            }
            if (!isset($routeParameters[$actionParameter->name()])) {
                throw new \InvalidArgumentException(sprintf(
                    'Action bound to route "%s" expects an required parameter $%s witch is not presented in route template',
                    $this->patternOrig,
                    $actionParameter->name()
                ));
            }
        }

        // Check action parameters and its types
        foreach ($routeParameters as $parameter) {
            if (!isset($actionParameters[$parameter->name()])) {
                throw new \InvalidArgumentException(sprintf(
                    'Route template "%s" contains parameter {%s} witch is not accepts by bound action',
                    $this->patternOrig,
                    $parameter->name()
                ));
            }
            $actionParameter = $actionParameters[$parameter->name()];
            if (!$parameter->checkActionParameterType($actionParameter)) {
                throw new \InvalidArgumentException(sprintf(
                    'Action bound to route "%s" expects parameter $%s type of "%s", witch does not match with type described in the route template',
                    $this->patternOrig,
                    $parameter->name(),
                    $actionParameter->type()
                ));
            }
        }
    }
}