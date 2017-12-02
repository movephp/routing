<?php

declare(strict_types=1);

namespace Movephp\Routing\Tests;

include_once(__DIR__ . '/fixtures.php');

use PHPUnit\Framework\TestCase;
use Movephp\Routing;
use Movephp\CallbackContainer\Container as CallbackContainer;

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
        $this->expectException(Routing\Exception\InvalidRouteClassException::class);

        $callbackContainerStub = $this->getMockBuilder(CallbackContainer::class)
            ->disableOriginalConstructor()
            ->getMock();
        new Routing\RouteBuilder($callbackContainerStub, $routeClass);
    }

    /**
     *
     */
    public function testInitWidthInvalidHttpMethods(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $callbackContainerStub = $this->getMockBuilder(CallbackContainer::class)
            ->disableOriginalConstructor()
            ->getMock();
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

        $callbackContainerStub = $this->getMockBuilder(CallbackContainer::class)
            ->disableOriginalConstructor()
            ->getMock();
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

        $callbackContainerStub = $this->getMockBuilder(CallbackContainer::class)
            ->disableOriginalConstructor()
            ->getMock();
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

        $callbackContainerStub = $this->getMockBuilder(CallbackContainer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $routeBuilder = new Routing\RouteBuilder($callbackContainerStub);
        $routeBuilder->call([]);
    }

    /**
     *
     */
    public function testCallWithoutPatterns(): void
    {
        $this->expectException(Routing\Exception\PatternNotSetException::class);

        $callbackContainerStub = $this->getMockBuilder(CallbackContainer::class)
            ->disableOriginalConstructor()
            ->getMock();
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
        $callbackActionStub = $this->getMockBuilder(CallbackContainer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $callbackContainerMock = $this->getMockBuilder(CallbackContainer::class)
            ->disableOriginalConstructor()
            ->setMethods(['make'])
            ->getMock();
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
                ['method1', 'pattern1', $callbackActionStub, null],
                ['method2', 'pattern1', $callbackActionStub, null],
                ['method1', 'pattern2', $callbackActionStub, null],
                ['method2', 'pattern2', $callbackActionStub, null]
            ],
            $routeMock::$constructorArgs
        );
    }

    /**
     *
     */
    public function testCallWithAuthorizationAndWithoutHttpMethods(): void
    {
        $callbackActionStub = $this->getMockBuilder(CallbackContainer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $callbackWhenStub = $this->getMockBuilder(CallbackContainer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $callbackContainerMock = $this->getMockBuilder(CallbackContainer::class)
            ->disableOriginalConstructor()
            ->setMethods(['make'])
            ->getMock();
        $callbackContainerMock->expects($this->exactly(2))
            ->method('make')
            ->with(
                $this->logicalOr($this->equalTo('myAction'), $this->equalTo('myWhen'))
            )
            ->will(
                $this->returnCallback(function ($c) use ($callbackActionStub, $callbackWhenStub) {
                    return [
                        'myAction' => $callbackActionStub,
                        'myWhen'   => $callbackWhenStub
                    ][$c];
                })
            );

        $routeMock = $this->routeMock();

        $routerMock = $this->getMockBuilder(Routing\Router::class)
            ->disableOriginalConstructor()
            ->setMethods(['add'])
            ->getMock();
        $routerMock->expects($this->once())
            ->method('add')
            ->with($this->isInstanceOf(get_class($routeMock)));

        $routeBuilder = new Routing\RouteBuilder($callbackContainerMock, get_class($routeMock));
        $routeBuilder->setRouter($routerMock);
        $routeBuilder->init([], ['pattern1']);
        $routeBuilder->when('myWhen')->call('myAction');

        $this->assertEquals(
            [
                ['', 'pattern1', $callbackActionStub, $callbackWhenStub]
            ],
            $routeMock::$constructorArgs
        );
    }

    /**
     *
     */
    public function testClearPatternsOnClone(): void
    {
        $callbackContainerMock = $this->getMockBuilder(CallbackContainer::class)
            ->disableOriginalConstructor()
            ->getMock();

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
     *
     */
    public function testClearAuthorizationOnClone(): void
    {
        $callbackActionStub = $this->getMockBuilder(CallbackContainer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $callbackContainerMock = $this->getMockBuilder(CallbackContainer::class)
            ->disableOriginalConstructor()
            ->setMethods(['make'])
            ->getMock();
        $callbackContainerMock->expects($this->any())
            ->method('make')
            ->willReturn($callbackActionStub);

        $routeMock = $this->routeMock();

        $routerMock = $this->getMockBuilder(Routing\Router::class)
            ->disableOriginalConstructor()
            ->getMock();

        $routeBuilder = new Routing\RouteBuilder($callbackContainerMock, get_class($routeMock));
        $routeBuilder->setRouter($routerMock);
        $routeBuilder->when('myWhen');

        $routeBuilder = clone($routeBuilder);
        $routeBuilder->init([], ['pattern1']);
        $routeBuilder->call('myAction');
        $this->assertEquals(
            [
                ['', 'pattern1', $callbackActionStub, null]
            ],
            $routeMock::$constructorArgs
        );
    }

    /**
     * @return Routing\Route\RouteInterface
     */
    private function routeMock(): Routing\Route\RouteInterface
    {
        $callbackContainerStub = $this->getMockBuilder(CallbackContainer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $routeMock = new class ('', '', $callbackContainerStub) implements Routing\Route\RouteInterface
        {
            public static $constructorArgs = [];

            public function __construct(
                string $httpMethod,
                string $pattern,
                CallbackContainer $action,
                CallbackContainer $authorization = null
            ) {
                self::$constructorArgs[] = func_get_args();
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