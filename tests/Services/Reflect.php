<?php

namespace FastD\Container\Tests\Services;

use DateTime;

/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */
class Reflect
{
    protected MethodInjection $methodInjection;

    public function __construct(MethodInjection $injection)
    {
        $this->methodInjection = $injection;
    }

    public function now(): string
    {
        $this->methodInjection->now(new DateTime());

        return $this->methodInjection->date;
    }
}
