<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__) . '/../vendor/autoload.php';

if (!class_exists(Dotenv::class)) {
    throw new LogicException(
        'You need to add "symfony/dotenv" as Composer dependency.'
    );
}

(new Dotenv())->bootEnv(dirname(__DIR__) . '/../.env');
