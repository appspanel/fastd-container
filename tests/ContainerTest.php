<?php
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

use FastD\Container\Container;
use FastD\Container\NotFoundException;
use FastD\Container\ServiceProviderInterface;
use PHPUnit\Framework\TestCase;

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

    // == add()
    // == get()
    public function testAddAndGetClass(): void
    {
        $this->container->add('class', Container::class);

        $this->assertInstanceOf(Container::class, $this->container->get('class'));
    }

    public function testAddAndGetObject(): void
    {
        $this->container->add('object', new Container());

        $this->assertInstanceOf(Container::class, $this->container->get('object'));
    }

    public function testAddAndGetClosure(): void
    {
        $this->container->add('closure', function()
        {
            return 'OK';
        });
        /** @var \Closure $closure */
        $closure = $this->container->get('closure');
        echo $closure();

        $this->expectOutputString('OK');
    }

    // == add()
    // == has()
    public function testHasClass(): void
    {
        $this->container->add('class', Container::class);

        $this->assertTrue($this->container->has('class'));
    }

    public function testHasObject(): void
    {
        $this->container->add('object', new Container());

        $this->assertTrue($this->container->has('object'));
    }

    public function testHasClosure(): void
    {
        $this->container->add('closure', function()
        {
            return 'OK';
        });

        $this->assertTrue($this->container->has('closure'));
    }

    // == register()
    public function testRegister(): void
    {
        $this->container->register(new class implements ServiceProviderInterface
        {
            public function register(Container $container): void
            {
                $container->add('class', Container::class);
                $container->add('object', new Container());
                $container->add('closure', function()
                {
                    return 'OK';
                });
            }
        });

        $this->assertInstanceOf(Container::class, $this->container->get('class'));
        $this->assertInstanceOf(Container::class, $this->container->get('object'));

        $closure = $this->container->get('closure');
        echo $closure();

        $this->expectOutputString('OK');
    }

    // == offsetExists()
    public function testOffsetExistsClass(): void
    {
        $this->container->add('class', Container::class);

        $this->assertTrue(isset($this->container['class']));
    }

    public function testOffsetExistsObject(): void
    {
        $this->container->add('object', new Container());

        $this->assertTrue(isset($this->container['object']));
    }

    public function testOffsetExistsClosure(): void
    {
        $this->container->add('closure', function()
        {
            return 'OK';
        });

        $this->assertTrue(isset($this->container['closure']));
    }

    // == offsetSet()
    // == offsetGet()
    public function testOffsetSetAndGetGetClass(): void
    {
        $this->container['class'] = Container::class;
        $container = $this->container['class'];

        $this->assertInstanceOf(Container::class, $container);
    }

    public function testOffsetSetAndGetGetObject(): void
    {
        $this->container['object'] = new Container();
        $container = $this->container['object'];

        $this->assertInstanceOf(Container::class, $container);
    }

    public function testOffsetSetAndGetGetClosure(): void
    {
        $this->container['closure'] = function()
        {
            return 'OK';
        };
        $closure = $this->container['closure'];
        echo $closure();

        $this->expectOutputString('OK');
    }

    // == offsetSet()
    // == offsetUnset()
    public function testOffsetSetAndUnsetClass(): void
    {
        $this->container['class'] = Container::class;
        unset($this->container['class']);

        $this->assertNotTrue(isset($this->container['class']));

        $this->expectException(NotFoundException::class);
        $c = $this->container['class'];
        $c = $this->container->get('class');
    }

    public function testOffsetSetAndUnsetObject(): void
    {
        $this->container['object'] = new Container();
        unset($this->container['object']);

        $this->assertNotTrue(isset($this->container['object']));

        $this->expectException(NotFoundException::class);
        $c = $this->container['object'];
        $c = $this->container->get('object');
    }

    public function testOffsetSetAndUnsetClosure(): void
    {
        $this->container['closure'] = function()
        {
            return 'OK';
        };
        unset($this->container['closure']);

        $this->assertNotTrue(isset($this->container['closure']));

        $this->expectException(NotFoundException::class);
        $c = $this->container['closure'];
        $c = $this->container->get('closure');
    }

    // == \Iterator
    public function testIterator(): void
    {
        $this->container->add('class', Container::class);
        $this->container->add('object', new Container());
        $this->container->add('closure', function()
        {
            return 'OK';
        });

        foreach($this->container as $key => $service) {
            $this->assertIsString($key);

            if('class' === $key || 'object' === $key) {
                $this->assertInstanceOf(Container::class, $service, $key.' is not a '.Container::class.'.');
            }
            elseif('closure' === $key) {
                echo $service();

                $this->expectOutputString('OK');
            }
            else {
                throw new RuntimeException('Unrecognized element.');
            }
        }
    }
}
