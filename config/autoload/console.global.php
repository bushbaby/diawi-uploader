<?php

return [
    'dependencies' => [
        'invokables' => [
        ],

        'factories' => [
            \App\Command\UploadCommand::class => \App\Command\Container\UploadCommandFactory::class,
            \App\Command\StatusCommand::class => \App\Command\Container\StatusCommandFactory::class,
        ],
    ],

    'console' => [
        'commands' => [
            \App\Command\UploadCommand::class,
            \App\Command\StatusCommand::class,
        ],
    ],
];
