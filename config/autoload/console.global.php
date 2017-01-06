<?php

return [
    'dependencies' => [
        'factories' => [
            \App\Command\ConfigureCommand::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \App\Command\UploadCommand::class    => \App\Command\Container\UploadCommandFactory::class,
            \App\Command\StatusCommand::class    => \App\Command\Container\StatusCommandFactory::class,
        ],
    ],

    'console' => [
        'commands' => [
            \App\Command\ConfigureCommand::class,
            \App\Command\UploadCommand::class,
            \App\Command\StatusCommand::class,
        ],
    ],
];
