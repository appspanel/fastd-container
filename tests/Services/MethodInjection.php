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
class MethodInjection
{
    public ?string $date = null;

    public function now(DateTime $date): static
    {
        $this->date = $date->format(DateTimeInterface::W3C);

        return $this;
    }
}
