<?php

declare(strict_types=1);

namespace Movephp\Routing\Route;

use Movephp\Routing\Exception;
use Movephp\CallbackContainer\ContainerInterface as CallbackContainer;

class Resolving implements ResolvingInterface
{
    /**
     * @var CallbackContainer
     */
    private $action;

    /**
     * @var CallbackContainer[]
     */
    private $filtersBefore = [];

    /**
     * @var CallbackContainer[]
     */
    private $filtersAfter = [];

    /**
     * @param CallbackContainer $action
     */
    public function setAction(CallbackContainer $action): void
    {
        $this->action = $action;
    }

    /**
     * @return CallbackContainer
     * @throws Exception\ResolvingActionIsNotSetException
     */
    public function getAction(): CallbackContainer
    {
        if (!$this->action) {
            throw new Exception\ResolvingActionIsNotSetException('Resolving action is not set');
        }
        return $this->action;
    }

    /**
     * @param CallbackContainer $callback
     */
    public function addFilterBefore(CallbackContainer $callback): void
    {
        $this->filtersBefore[] = $callback;
    }

    /**
     * @return CallbackContainer[]
     */
    public function getFiltersBefore(): array
    {
        return $this->filtersBefore;
    }

    /**
     * @param CallbackContainer $callback
     */
    public function addFilterAfter(CallbackContainer $callback): void
    {
        $this->filtersAfter[] = $callback;
    }

    /**
     * @return CallbackContainer[]
     */
    public function getFiltersAfter(): array
    {
        return $this->filtersAfter;
    }

    /**
     * @return bool
     */
    public function isSerializable(): bool
    {
        $callbacks = array_merge(
            [$this->action],
            $this->filtersBefore,
            $this->filtersAfter
        );
        return
            array_reduce(
                $callbacks,
                function ($result, CallbackContainer $callback) {
                    return $result && $callback->isSerializable();
                },
                true
            );
    }
}