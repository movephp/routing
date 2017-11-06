<?php

namespace Movephp\Routing;

trait ControllerTrait
{
    /**
     * @return self
     */
    public function method()
    {
        return new MethodPointing($this);
    }

    /**
     * @return self
     */
    public function url()
    {
        //return new MethodPointing(get_called_class(), true);
    }
}