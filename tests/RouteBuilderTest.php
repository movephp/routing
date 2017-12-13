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
            'NonExistentClass'                   => ['NonExistentClass'],
            'RouteClassNotImplementingInterface' => [Fixtures\RouteClassNotImplementingInterface::class],
            'RouteClassNonInstantiable'          => [Fixtures\RouteClassNonInstantiable::class]
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
        $resolvingFactoryStub = $this->createMock(Routing\Route\ResolvingFactory::class);
        new Routing\RouteBuilder($callbackContainerStub, $resolvingFactoryStub, $routeClass);
    }

    /**
     *
     */
    public function testInitWithInvalidHttpMethods(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $callbackContainerStub = $this->getMockForAbstractClass(CallbackContainer::class);
        $resolvingFactoryStub = $this->createMock(Routing\Route\ResolvingFactory::class);
        $routeBuilder = new Routing\RouteBuilder($callbackContainerStub, $resolvingFactoryStub);
        $routeBuilder->init(
            [['non-string']],
            ['some-pattern']
        );
    }

    /**
     *
     */
    public function testInitWithInvalidPatterns(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $callbackContainerStub = $this->getMockForAbstractClass(CallbackContainer::class);
        $resolvingFactoryStub = $this->createMock(Routing\Route\ResolvingFactory::class);
        $routeBuilder = new Routing\RouteBuilder($callbackContainerStub, $resolvingFactoryStub);
        $routeBuilder->init(
            ['some-method'],
            [['non-string']]
        );
    }

    /**
     *
     */
    public function testInitWithEmptyPatterns(): void
    {
        $this->expectException(Routing\Exception\PatternNotSetException::class);
        $callbackContainerStub = $this->getMockForAbstractClass(CallbackContainer::class);
        $resolvingFactoryStub = $this->createMock(Routing\Route\ResolvingFactory::class);
        $routeBuilder = new Routing\RouteBuilder($callbackContainerStub, $resolvingFactoryStub);
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
        $resolvingFactoryStub = $this->createMock(Routing\Route\ResolvingFactory::class);
        $routeBuilder = new Routing\RouteBuilder($callbackContainerStub, $resolvingFactoryStub);
        $routeBuilder->call([]);
    }

    /**
     *
     */
    public function testCallWithoutPatterns(): void
    {
        $this->expectException(Routing\Exception\PatternNotSetException::class);
        $callbackContainerStub = $this->getMockForAbstractClass(CallbackContainer::class);
        $resolvingFactoryStub = $this->createMock(Routing\Route\ResolvingFactory::class);
        $routerStub = $this->getMockBuilder(Routing\Router::class)
            ->disableOriginalConstructor()
            ->getMock();
        $routeBuilder = new Routing\RouteBuilder($callbackContainerStub, $resolvingFactoryStub);
        $routeBuilder->setRouter($routerStub);
        $routeBuilder->call([]);
    }

    /**
     *
     */
    public function testFilter(): void
    {
        $callbackStub = $this->getMockForAbstractClass(CallbackContainer::class);

        $callbackContainerMock = $this->getMockForAbstractClass(CallbackContainer::class);
        $callbackContainerMock->expects($this->once())
            ->method('make')
            ->with('myFilterBefore')
            ->willReturn($callbackStub);

        $resolvingMock = $this->getMockForAbstractClass(Routing\Route\ResolvingInterface::class);
        $resolvingMock->expects($this->once())
            ->method('addFilterBefore')
            ->with($callbackStub);

        $resolvingFactoryMock = $this->getMockBuilder(Routing\Route\ResolvingFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['getCleanResolving'])
            ->getMock();
        $resolvingFactoryMock->expects($this->any())
            ->method('getCleanResolving')
            ->willReturn($resolvingMock);

        $routeBuilder = new Routing\RouteBuilder($callbackContainerMock, $resolvingFactoryMock);
        $routeBuilder->filter('myFilterBefore');
    }

    /**
     *
     */
    public function testOut(): void
    {
        $callbackStub = $this->getMockForAbstractClass(CallbackContainer::class);

        $callbackContainerMock = $this->getMockForAbstractClass(CallbackContainer::class);
        $callbackContainerMock->expects($this->once())
            ->method('make')
            ->with('myFilterAfter')
            ->willReturn($callbackStub);

        $resolvingMock = $this->getMockForAbstractClass(Routing\Route\ResolvingInterface::class);
        $resolvingMock->expects($this->once())
            ->method('addFilterAfter')
            ->with($callbackStub);

        $resolvingFactoryMock = $this->getMockBuilder(Routing\Route\ResolvingFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['getCleanResolving'])
            ->getMock();
        $resolvingFactoryMock->expects($this->any())
            ->method('getCleanResolving')
            ->willReturn($resolvingMock);

        $routeBuilder = new Routing\RouteBuilder($callbackContainerMock, $resolvingFactoryMock);
        $routeBuilder->out('myFilterAfter');
    }

    /**
     *
     */
    public function testCall(): void
    {
        $callbackStub = $this->getMockForAbstractClass(CallbackContainer::class);

        $callbackContainerMock = $this->getMockForAbstractClass(CallbackContainer::class);
        $callbackContainerMock->expects($this->once())
            ->method('make')
            ->with($this->equalTo('myAction'))
            ->willReturn($callbackStub);

        $resolvingMock = $this->getMockForAbstractClass(Routing\Route\ResolvingInterface::class);
        $resolvingMock->expects($this->once())
            ->method('setAction')
            ->with($callbackStub);

        $resolvingFactoryMock = $this->getMockBuilder(Routing\Route\ResolvingFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['getCleanResolving'])
            ->getMock();
        $resolvingFactoryMock->expects($this->any())
            ->method('getCleanResolving')
            ->willReturn($resolvingMock);

        $routeMock = $this->routeMock();

        $routerMock = $this->getMockBuilder(Routing\Router::class)
            ->disableOriginalConstructor()
            ->setMethods(['add'])
            ->getMock();
        $routerMock->expects($this->exactly(4))
            ->method('add')
            ->with($this->isInstanceOf(get_class($routeMock)));

        $routeBuilder = new Routing\RouteBuilder($callbackContainerMock, $resolvingFactoryMock, get_class($routeMock));
        $routeBuilder->setRouter($routerMock);
        $routeBuilder->init(
            ['method1', 'method2'],
            ['pattern1', 'pattern2']
        );
        $routeBuilder->call('myAction');

        $this->assertEquals(
            [
                ['method1', 'pattern1', $resolvingMock],
                ['method2', 'pattern1', $resolvingMock],
                ['method1', 'pattern2', $resolvingMock],
                ['method2', 'pattern2', $resolvingMock]
            ],
            $routeMock::$constructorArgs
        );
    }

    /**
     *
     */
    public function testCallWidthEmptyMethods(): void
    {
        $callbackContainerStub = $this->getMockForAbstractClass(CallbackContainer::class);
        $resolvingStub = $this->getMockForAbstractClass(Routing\Route\ResolvingInterface::class);

        $resolvingFactoryMock = $this->getMockBuilder(Routing\Route\ResolvingFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['getCleanResolving'])
            ->getMock();
        $resolvingFactoryMock->expects($this->any())
            ->method('getCleanResolving')
            ->willReturn($resolvingStub);

        $routerStub = $this->getMockBuilder(Routing\Router::class)
            ->disableOriginalConstructor()
            ->getMock();

        $routeMock = $this->routeMock();

        $routeBuilder = new Routing\RouteBuilder($callbackContainerStub, $resolvingFactoryMock, get_class($routeMock));
        $routeBuilder->setRouter($routerStub);
        $routeBuilder->init([], ['pattern']);
        $routeBuilder->call('myAction');
        $this->assertEquals(
            [
                ['', 'pattern', $resolvingStub]
            ],
            $routeMock::$constructorArgs
        );
    }

    /**
     *
     */
    public function testCallTwice(): void
    {
        $callbackContainerStub = $this->getMockForAbstractClass(CallbackContainer::class);
        $resolvingFactoryStub = $this->createMock(Routing\Route\ResolvingFactory::class);
        $routerStub = $this->getMockBuilder(Routing\Router::class)
            ->disableOriginalConstructor()
            ->getMock();
        $routeStub = $this->routeMock();

        $routeBuilder = new Routing\RouteBuilder($callbackContainerStub, $resolvingFactoryStub, get_class($routeStub));
        $routeBuilder->setRouter($routerStub);
        $routeBuilder->init([], ['pattern']);
        $routeBuilder->call('myAction1');

        $this->expectException(\BadMethodCallException::class);
        $routeBuilder->call('myAction2');
    }

    /**
     *
     */
    public function testClearPatternsOnClone(): void
    {
        $callbackContainerStub = $this->getMockForAbstractClass(CallbackContainer::class);
        $resolvingFactoryStub = $this->createMock(Routing\Route\ResolvingFactory::class);
        $routerStub = $this->getMockBuilder(Routing\Router::class)
            ->disableOriginalConstructor()
            ->getMock();

        $routeBuilder = new Routing\RouteBuilder($callbackContainerStub, $resolvingFactoryStub);
        $routeBuilder->setRouter($routerStub);
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