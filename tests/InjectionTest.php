<?php

namespace FastD\Container\Tests;

use DateTime;
use DateTimeInterface;
use DateTimeZone;
use FastD\Container\Injection;
use FastD\Container\Tests\Services\ConstructorInjection;
use FastD\Container\Tests\Services\MethodInjection;
use FastD\Container\Tests\Services\StaticInjection;
use PHPUnit\Framework\TestCase;

/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */
class InjectionTest extends TestCase
{
    public function testConstructInjection(): void
    {
        $injection = new Injection(ConstructorInjection::class);
        $injection->withConstruct()->withArguments([
            new DateTime(),
        ]);
        $obj = $injection->make();

        $this->assertEquals($obj->now(), (new DateTime())->format(DateTimeInterface::W3C));
    }

    public function testMethodInjection(): void
    {
        $injection = new Injection(MethodInjection::class);
        $injection->withMethod('now')->withArguments([
            new DateTime(),
        ]);
        $obj = $injection->make();

        $this->assertEquals($obj->date, (new DateTime())->format(DateTimeInterface::W3C));
    }

    public function testStaticInjection(): void
    {
        $injection = new Injection(StaticInjection::class);
        $injection->withMethod('now', true)->withArguments([
            new DateTime(),
        ]);
        $injection->make();

        $this->assertEquals(StaticInjection::$date, (new DateTime())->format(DateTimeInterface::W3C));
    }

    public function testClosureInjection(): void
    {
        $injection = new Injection(function(DateTimeZone $dateTimeZone)
        {
            return new DateTime('now', $dateTimeZone);
        });
        $injection2 = clone $injection;

        $injection->withArguments([
            new DateTimeZone('PRC'),
        ]);
        $injection2->withArguments([
            new DateTimeZone('UTC'),
        ]);

        $date1 = $injection->make();
        $date2 = $injection2->make();

        $this->assertEquals('UTC', $date2->getTimeZone()->getName());
        $this->assertEquals('PRC', $date1->getTimeZone()->getName());
    }

    public function testInstanceInjection(): void
    {
        $injection = new Injection(new MethodInjection());
        $injection
            ->withMethod('now')
            ->withArguments([
                new DateTime(),
            ])
        ;
        $obj = $injection->make();

        $this->assertEquals($obj->date, (new DateTime())->format(DateTimeInterface::W3C));
    }
}
