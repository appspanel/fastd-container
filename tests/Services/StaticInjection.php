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
class StaticInjection
{
    static ?string $date = null;

    public static function now(DateTime $dateTime): void
    {
        static::$date = $dateTime->format(DateTimeInterface::W3C);
    }
}
