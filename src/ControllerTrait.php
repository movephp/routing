<?php

declare(strict_types=1);

namespace Movephp\Routing;

trait ControllerTrait
{
    /**
     * @return self
     */
    public static function method()
    {
        return new RouteActionPointer(get_called_class());
    }

    /**
     * @return self
     */
    /*public function url()
    {
        return new RouteActionPointer(get_called_class(), true);
    }*/
}