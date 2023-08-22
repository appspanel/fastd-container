<?php

namespace FastD\Container\Tests\Services;

use DateTimeZone;
use FastD\Container\Container;
use FastD\Container\ServiceProviderInterface;

/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2017
 *
 * @see      https://www.github.com/janhuang
 * @see      http://www.fast-d.cn/
 */
class TestServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        $container->add('timezone', new DateTimeZone('PRC'));
    }
}
