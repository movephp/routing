<?php

declare(strict_types=1);

namespace Movephp\Routing\Tests;

use PHPUnit\Framework\TestCase;
use Movephp\Routing;

/**
 * Class ControllerTraitTest
 * @package Movephp\Routing\Tests
 */
class ControllerTraitTest extends TestCase
{
    /**
     * @var mixed
     */
    private $mock = null;

    /**
     *
     */
    protected function setUp(): void
    {
        $this->mock = new class
        {
            use Routing\ControllerTrait;
        };
    }

    /**
     *
     */
    public function testMethod(): void
    {
        $this->assertEquals(
            [get_class($this->mock), 'someMethod'],
            $this->mock::method()->someMethod()
        );
    }

    /**
     *
     */
    public function testMethodWithArgs(): void
    {
        $this->expectException(Routing\Exception\ArgumentsNotAllowedException::class);
        $this->mock::method()->someMethod('some-argument');
    }
}