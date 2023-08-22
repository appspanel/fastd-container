<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Container;

use Psr\Container\ContainerInterface;

/**
 * Class ContainerAware
 *
 * @package FastD\Container
 */
trait ContainerAware
{
    /**
     * @var \Psr\Container\ContainerInterface
     */
    protected ContainerInterface $container;

    /**
     * @param \Psr\Container\ContainerInterface $container
     * @return $this
     */
    public function withContainer(ContainerInterface $container): static
    {
        $this->container = $container;

        return $this;
    }

    /**
     * @return \Psr\Container\ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }
}
