<?php

declare(strict_types=1);

namespace Movephp\Routing\Route;

use Movephp\Routing\Router;

/**
 * Class ResolvingFactory
 * @package Movephp\Routing\Route
 */
class ResolvingFactory {
    /**
     * @var string
     */
    private $resolvingClass = Resolving::class;

    /**
     * ResolvingFactory constructor.
     * @param string $resolvingClass
     */
    public function __construct(string $resolvingClass = '') {

        if ($resolvingClass && Router::checkClassName($resolvingClass, ResolvingInterface::class)) {
            $this->resolvingClass = $resolvingClass;
        }
    }

    /**
     * @return ResolvingInterface
     */
    public function getCleanResolving(): ResolvingInterface {
        return new $this->resolvingClass();
    }
}