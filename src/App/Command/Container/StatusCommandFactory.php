<?php

declare(strict_types = 1);

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
