<?php

namespace LiteCQRS\Plugin\SymfonyBundle;

use LiteCQRS\Bus\CommandBus;
use LiteCQRS\EventStore\IdentityMapInterface;
use LiteCQRS\EventStore\EventStoreInterface;

use Symfony\Component\DependencyInjection\ContainerInterface;

class ContainerCommandBus extends CommandBus
{
    private $container;
    private $commandServices;

    public function __construct(ContainerInterface $container, EventStoreInterface $eventStore, IdentityMapInterface $identityMap = null, $proxyFactory = null)
    {
        parent::__construct($eventStore, $identityMap);
        $this->container = $container;
    }

    public function registerServices($commandServices)
    {
        $this->commandServices = $commandServices;
    }

    protected function getService($commandType)
    {
        if (!isset($this->commandServices[$commandType])) {
            throw new \RuntimeException("No command handler exists for command '" . $commandType . "'");
        }

        $serviceId = $this->commandServices[$commandType];

        if (!$this->container->has($serviceId)) {
            throw new \RuntimeException("Symfony Service Container has no service '".$serviceId."' that is registered for command '". $commandType . "'");
        }

        return $this->container->get($serviceId);
    }
}
