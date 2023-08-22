<?php

namespace FastD\Container\Tests;


/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/3/11 仅以此时，怀念过去的自己
 * Time: 下午2:38
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 */

use DateTime;
use DateTimeZone;
use FastD\Container\Container;
use FastD\Container\NotFoundException;
use FastD\Container\Tests\Services\TestServiceProvider;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class ContainerTest extends TestCase
{
    /**
     * @var \FastD\Container\Container The test container.
     */
    protected Container $container;

    public function setUp(): void
    {
        $this->container = new Container();
    }

    public function testContainerClassString(): void
    {
        $this->container->add('timezone', DateTimeZone::class);

        $this->assertTrue($this->container->has('timezone'));
        $this->assertEquals(DateTimeZone::class, $this->container->get('timezone'));
    }

    public function testContainerClassHashStatus(): void
    {
        $this->container->add('timezone', DateTimeZone::class);

        $this->assertTrue($this->container->has('timezone'));
        $this->assertTrue($this->container->has(DateTimeZone::class));
    }

    public function testContainerMakeClassString(): void
    {
        $this->container->add('timezone', DateTimeZone::class);
        $timezone = $this->container->make('timezone', ['PRC']);

        $this->assertInstanceOf(DateTimeZone::class, $timezone);
        $this->assertEquals('PRC', $timezone->getName());
    }

    public function testContainerClosure(): void
    {
        $this->container->add('timezone', static function (): DateTimeZone {
            return new DateTimeZone('UTC');
        });

        $this->container->add('date', function (): DateTime {
            return new DateTime('now', $this->container->get('timezone'));
        });

        $this->assertEquals(new DateTimeZone('UTC'), $this->container->get('timezone'));
        $this->assertInstanceOf(DateTimeZone::class, $this->container->get('timezone'));
        $this->assertEquals('UTC', $this->container->get('date')->getTimeZone()->getName());
    }

    public function testContainerObject(): void
    {
        $this->container->add('timezone', new DateTimeZone('PRC'));

        $this->assertTrue($this->container->has('timezone'));
        $this->assertInstanceOf(DateTimeZone::class, $this->container->get('timezone'));
        $this->assertEquals('PRC', $this->container->get('timezone')->getName());
    }

    public function testContainerRegister(): void
    {
        $this->container->register(new TestServiceProvider());

        $this->assertTrue($this->container->has('timezone'));
        $this->assertInstanceOf(DateTimeZone::class, $this->container->get('timezone'));
    }

    public function testOffsetExistsClassString(): void
    {
        $this->container->add('timezone', DateTimeZone::class);

        $this->assertTrue(isset($this->container['timezone']));
    }

    public function testOffsetExistsObject(): void
    {
        $this->container->add('timezone', new DateTimeZone('PRC'));

        $this->assertTrue(isset($this->container['timezone']));
    }

    public function testOffsetSetAndGetGetClassString(): void
    {
        $this->container['timezone'] = DateTimeZone::class;
        $timeZone = $this->container['timezone'];

        $this->assertEquals(DateTimeZone::class, $timeZone);
    }

    public function testOffsetSetAndGetGetObject(): void
    {
        $this->container['timezone'] = new DateTimeZone('PRC');
        $timezone = $this->container['timezone'];

        $this->assertInstanceOf(DateTimeZone::class, $timezone);
        $this->assertEquals('PRC', $timezone->getName());
    }

    public function testOffsetSetAndUnsetClassString(): void
    {
        $this->container['timezone'] = DateTimeZone::class;
        unset($this->container['timezone']);

        $this->assertNotTrue(isset($this->container['timezone']));

        $this->expectException(NotFoundException::class);
        /** @noinspection PhpUnusedLocalVariableInspection */
        $c = $this->container['timezone'];
        /** @noinspection PhpUnusedLocalVariableInspection */
        $c = $this->container->get('timezone');
    }

    public function testOffsetSetAndUnsetObject(): void
    {
        $this->container['timezone'] = new DateTimeZone('PRC');
        unset($this->container['timezone']);

        $this->assertNotTrue(isset($this->container['timezone']));

        $this->expectException(NotFoundException::class);
        /** @noinspection PhpUnusedLocalVariableInspection */
        $c = $this->container['timezone'];
        /** @noinspection PhpUnusedLocalVariableInspection */
        $c = $this->container->get('timezone');
    }

    public function testIterator(): void
    {
        $this->container->add('class', DateTimeZone::class);
        $this->container->add('object', new DateTimeZone('PRC'));
        $this->container->add('closure', static function(): string {
            return 'OK';
        });

        foreach($this->container as $key => $service) {
            $this->assertIsString($key);

            if('class' === $key) {
                $this->assertEquals(DateTimeZone::class, $service, $key.' is not a '.DateTimeZone::class.'.');
            }
            elseif('object' === $key) {
                $this->assertInstanceOf(DateTimeZone::class, $service);
            }
            elseif('closure' === $key) {
                $this->assertEquals('OK', $service);
            }
            else {
                throw new RuntimeException('Unrecognized element.');
            }
        }
    }
}
