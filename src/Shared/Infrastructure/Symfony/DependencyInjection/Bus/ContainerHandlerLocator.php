<?php

namespace Jcv\Shared\Infrastructure\Symfony\DependencyInjection\Bus;

use League\Tactician\Handler\Locator\HandlerLocator;
use Psr\Container\ContainerInterface;

class ContainerHandlerLocator implements HandlerLocator
{
    protected ContainerInterface $container;
    private string $commandSuffix;
    private string $commandHandlerSuffix;

    public function __construct(
        ContainerInterface $container,
        string $commandSuffix,
        string $commandHandlerSuffix
    ) {
        $this->container = $container;
        $this->commandSuffix = $commandSuffix;
        $this->commandHandlerSuffix = $commandHandlerSuffix;
    }

    public function getHandlerForCommand($commandName)
    {
        $serviceId = preg_replace('/' . $this->commandSuffix . '$/', $this->commandHandlerSuffix, $commandName);

        return $this->container->get($serviceId);
    }
}

