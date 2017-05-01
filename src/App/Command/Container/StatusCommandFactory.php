<?php

namespace App\Command\Container;

use App\Command\StatusCommand;
use Interop\Container\ContainerInterface;

class StatusCommandFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new StatusCommand(
            $container->get('config')['diawi']
        );
    }
}
