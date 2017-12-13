<?php

namespace Movephp\Routing\Tests\Fixtures;

use Movephp\Routing;

class RouteClassNotImplementingInterface
{

}

abstract class RouteClassNonInstantiable implements Routing\Route\RouteInterface
{

}

class ResolvingClassNotImplementingInterface
{

}

abstract class ResolvingClassNonInstantiable implements Routing\Route\ResolvingInterface
{

}