<?php

declare(strict_types=1);

namespace Movephp\Routing\Route;

use Movephp\Routing\Exception;
use Movephp\CallbackContainer\ContainerInterface as CallbackContainer;

interface ResolvingInterface
{
    /**
     * @param CallbackContainer $action
     */
    public function setAction(CallbackContainer $action): void;

    /**
     * @return CallbackContainer
     * @throws Exception\ResolvingActionIsNotSetException
     */
    public function getAction(): CallbackContainer;

    /**
     * @param CallbackContainer $callback
     */
    public function addFilterBefore(CallbackContainer $callback): void;

    /**
     * @return CallbackContainer[]
     */
    public function getFiltersBefore(): array;

    /**
     * @param CallbackContainer $callback
     */
    public function addFilterAfter(CallbackContainer $callback): void;

    /**
     * @return CallbackContainer[]
     */
    public function getFiltersAfter(): array;

    /**
     * @return bool
     */
    public function isSerializable(): bool;
}