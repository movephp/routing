<?php

declare(strict_types=1);

namespace Movephp\Routing\Tests;

include_once(__DIR__ . '/fixtures.php');

use PHPUnit\Framework\TestCase;
use Movephp\Routing;
use Movephp\CallbackContainer\ContainerInterface as CallbackContainer;

/**
 * Class RouteBuilderTest
 * @package Movephp\Routing\Tests
 */
class RouteBuilderTest extends TestCase
{

    /**
     * @return array
     */
    public function routeClassProvider(): array
    {
        return [
            ['NonExistentClass'],
            [Fixtures\RouteClassNotImplementingInterface::class],
            [Fixtures\RouteClassNonInstantiable::class]
        ];
    }

    /**
     * @param string $routeClass
     * @dataProvider routeClassProvider
     */
    public function testConstructorWithInvalidRouteClass(string $routeClass): void
    {
        $this->expectException(Routing\Exception\InvalidClassException::class);

        $callbackContainerStub = $this->getMockForAbstractClass(CallbackContainer::class);
        new Routing\RouteBuilder($callbackContainerStub, $routeClass);
    }

    /**
     *
     */
    public function testInitWidthInvalidHttpMethods(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $callbackContainerStub = $this->getMockForAbstractClass(CallbackContainer::class);
        $routeBuilder = new Routing\RouteBuilder($callbackContainerStub);
        $routeBuilder->init(
            [['non-string']],
            ['some-pattern']
        );
    }

    /**
     *
     */
    public function testInitWidthInvalidPatterns(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $callbackContainerStub = $this->getMockForAbstractClass(CallbackContainer::class);
        $routeBuilder = new Routing\RouteBuilder($callbackContainerStub);
        $routeBuilder->init(
            ['some-method'],
            [['non-string']]
        );
    }

    /**
     *
     */
    public function testInitWidthEmptyPatterns(): void
    {
        $this->expectException(Routing\Exception\PatternNotSetException::class);

        $callbackContainerStub = $this->getMockForAbstractClass(CallbackContainer::class);
        $routeBuilder = new Routing\RouteBuilder($callbackContainerStub);
        $routeBuilder->init(
            ['some-method'],
            []
        );
    }

    /**
     *
     */
    public function testCallWithoutRouter(): void
    {
        $this->expectException(Routing\Exception\RouterNotSetException::class);

        $callbackContainerStub = $this->getMockForAbstractClass(CallbackContainer::class);
        $routeBuilder = new Routing\RouteBuilder($callbackContainerStub);
        $routeBuilder->call([]);
    }

    /**
     *
     */
    public function testCallWithoutPatterns(): void
    {
        $this->expectException(Routing\Exception\PatternNotSetException::class);

        $callbackContainerStub = $this->getMockForAbstractClass(CallbackContainer::class);
        $routerStub = $this->getMockBuilder(Routing\Router::class)
            ->disableOriginalConstructor()
            ->getMock();
        $routeBuilder = new Routing\RouteBuilder($callbackContainerStub);
        $routeBuilder->setRouter($routerStub);
        $routeBuilder->call([]);
    }

    /**
     *
     */
    public function testCall(): void
    {
        $callbackActionStub = $this->getMockForAbstractClass(CallbackContainer::class);

        $callbackContainerMock = $this->getMockForAbstractClass(CallbackContainer::class);
        $callbackContainerMock->expects($this->once())
            ->method('make')
            ->with($this->equalTo('myAction'))
            ->willReturn($callbackActionStub);

        $routeMock = $this->routeMock();

        $routerMock = $this->getMockBuilder(Routing\Router::class)
            ->disableOriginalConstructor()
            ->setMethods(['add'])
            ->getMock();
        $routerMock->expects($this->exactly(4))
            ->method('add')
            ->with($this->isInstanceOf(get_class($routeMock)));

        $routeBuilder = new Routing\RouteBuilder($callbackContainerMock, get_class($routeMock));
        $routeBuilder->setRouter($routerMock);
        $routeBuilder->init(
            ['method1', 'method2'],
            ['pattern1', 'pattern2']
        );
        $routeBuilder->call('myAction');

        $this->assertEquals(
            [
                ['method1', 'pattern1', $callbackActionStub],
                ['method2', 'pattern1', $callbackActionStub],
                ['method1', 'pattern2', $callbackActionStub],
                ['method2', 'pattern2', $callbackActionStub]
            ],
            $routeMock::$constructorArgs
        );
    }

    /**
     *
     */
    public function testClearPatternsOnClone(): void
    {
        $callbackContainerMock = $this->getMockForAbstractClass(CallbackContainer::class);

        $routeMock = $this->routeMock();

        $routerMock = $this->getMockBuilder(Routing\Router::class)
            ->disableOriginalConstructor()
            ->getMock();

        $routeBuilder = new Routing\RouteBuilder($callbackContainerMock, get_class($routeMock));
        $routeBuilder->setRouter($routerMock);
        $routeBuilder->init([], ['pattern1']);

        $this->expectException(Routing\Exception\PatternNotSetException::class);
        $routeBuilder = clone($routeBuilder);
        $routeBuilder->call('myAction');
    }

    /**
     * @return Routing\Route\RouteInterface
     */
    private function routeMock(): Routing\Route\RouteInterface
    {
        $resolvingStub = $this->getMockForAbstractClass(Routing\Route\ResolvingInterface::class);

        $routeMock = new class ('', '', $resolvingStub) implements Routing\Route\RouteInterface
        {
            public static $constructorArgs = [];

            public function __construct(
                string $httpMethod,
                string $pattern,
                Routing\Route\ResolvingInterface $resolvingStub
            ) {
                self::$constructorArgs[] = func_get_args();
            }

            /**
             * @param CallbackContainer $callback
             */
            public function addFilterBefore(CallbackContainer $callback): void
            {
            }

            /**
             * @param CallbackContainer $callback
             */
            public function addFilterAfter(CallbackContainer $callback): void
            {
            }

            public function isSerializable(): bool
            {
                return false;
            }
        };
        $routeMock::$constructorArgs = [];
        return $routeMock;
    }
}