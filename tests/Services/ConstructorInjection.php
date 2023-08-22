<?php

namespace FastD\Container\Tests\Services;

use DateTime;
use DateTimeInterface;

/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */
class ConstructorInjection
{
    public string $date;

    public function __construct(DateTime $date)
    {
        $this->date = $date->format(DateTimeInterface::W3C);
    }

    public function now(): string
    {
        return $this->date;
    }
}
