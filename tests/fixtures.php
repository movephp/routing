<?php

namespace Movephp\Routing\Tests\Fixtures;

use Movephp\Routing;

class RouteClassNotImplementingInterface
{

}

abstract class RouteClassNonInstantiable implements Routing\Route\RouteInterface
{

}