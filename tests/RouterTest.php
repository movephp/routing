<?php

declare(strict_types=1);

namespace Movephp\Routing\Tests;

use PHPUnit\Framework\TestCase;
use Movephp\Routing;

/**
 * Class RouterTest
 * @package Movephp\Routing\Tests
 */
class RouterTest extends TestCase
{
    /**
     *
     */
    public function testConstructor(): void
    {
        $routeBuilderMock = $this->getMockBuilder(Routing\RouteBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['setRouter'])
            ->getMock();
        $routeBuilderMock->expects($this->once())
            ->method('setRouter')
            ->with($this->isInstanceOf(Routing\Router::class));

        new Routing\Router($routeBuilderMock);
    }

    /**
     *
     */
    public function testOnMatch(): void
    {
        $methods = ['method1', 'method2', 'method3'];
        $patterns = ['pattern1', 'pattern2', 'pattern3'];

        $routeBuilderMock = $this->getMockBuilder(Routing\RouteBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['init'])
            ->getMock();
        $routeBuilderMock->expects($this->once())
            ->method('init')
            ->with($this->equalTo($methods), $this->equalTo($patterns));

        $router = new Routing\Router($routeBuilderMock);
        $router->onMatch($methods, ...$patterns);
    }

    /**
     * @return array
     */
    public function onSmthProvider(): array
    {
        return [
            ['onGet', ['GET']],
            ['onPost', ['POST']],
            ['onPut', ['PUT']],
            ['onDelete', ['DELETE']],
            ['onOptions', ['OPTIONS']],
            ['onAny', []],
        ];
    }

    /**
     * @param string $testingMethod
     * @param array $methods
     * @dataProvider onSmthProvider
     */
    public function testOnSmth(string $testingMethod, array $methods): void
    {
        $patterns = ['pattern1', 'pattern2', 'pattern3'];

        $routeBuilderMock = $this->getMockBuilder(Routing\RouteBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['init'])
            ->getMock();
        $routeBuilderMock->expects($this->once())
            ->method('init')
            ->with($this->equalTo($methods), $this->equalTo($patterns));

        $router = new Routing\Router($routeBuilderMock);
        $router->$testingMethod(...$patterns);
    }

    /**
     *
     */
    public function testAdd(): void
    {
        $routeBuilderMock = $this->getMockBuilder(Routing\RouteBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();
        $router = new Routing\Router($routeBuilderMock);

        $routeMock1 = $this->getMockForAbstractClass(Routing\Route\RouteInterface::class);
        $routeMock2 = $this->getMockForAbstractClass(Routing\Route\RouteInterface::class);

        $router->add($routeMock1);
        $router->add($routeMock2);
        $this->assertEquals(
            [$routeMock1, $routeMock2],
            $router->getRoutes()
        );
    }
}