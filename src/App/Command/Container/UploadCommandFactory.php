<?php

declare(strict_types = 1);

namespace App\Command\Container;

use App\Command\UploadCommand;
use Interop\Container\ContainerInterface;

class UploadCommandFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new UploadCommand(
            $container->get('config')['diawi']
        );
    }
}
