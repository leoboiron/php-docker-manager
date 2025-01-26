<?php

require_once __DIR__ . '/../vendor/autoload.php';

use PhpDockerManager\Container\Config;
use PhpDockerManager\Manager;

// Create the manager with the Unix socket.
$manager = new Manager('unix:///var/run/docker.sock');

// Create a configuration for the container.
// Image name and tag are required.
$config = new Config('phpmyadmin/phpmyadmin', 'latest');

// Add some options.
$config
    ->setName('my-phpmyadmin')
    ->addLabel('traefik.enable', 'true')
    ->setNetwork('traefik-proxy')
    ->addEnvVar('PMA_ARBITRARY', '1')
;

// Run the container.
$instance = $manager->run($config);

// Get the status and id of the container.
echo 'Container status: ' . $manager->getStatus($instance)->name . PHP_EOL;
echo 'Container ID: ' . $instance->getId() . PHP_EOL;

// Remove the container.
$manager->remove($instance);
