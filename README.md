# PHP Docker Manager

**PHP Docker Manager** is a lightweight PHP library that provides an easy way to manage Docker containers (run/remove) from your PHP applications.

I developed this library for my personal needs. In consequence, it is not a complete Docker API client. It only provides the features I needed. See the API section for more details. Feel free to contribute to this project if you need more features.

Only the Docker Unix socket is supported. The TCP socket is not yet supported.

## Requirements

- PHP extension `sockets` and `curl` must be enabled.
- Docker socket rights: the user running the PHP script must be in the `docker` group or have the rights to access the Docker socket.

## Installation

Install the library using Composer:

```bash
composer require leoboiron/php-docker-manager
```

## Quick start

<!--
$container = new Config('phpmyadmin/phpmyadmin', 'latest');

$container
    ->setName('my-phpmyadmin')
    ->addLabel('traefik.enable', 'true')
    ->setNetwork('traefik')
    ->addEnvVar('PMA_ARBITRARY', '1')
; -->

```php
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
    ->setNetwork('traefik')
    ->addEnvVar('PMA_ARBITRARY', '1')
;

// Run the container.
$instance = $manager->run($config);

// Get the status and id of the container.
echo 'Container status: ' . $manager->getStatus($instance)->name . PHP_EOL;
echo 'Container ID: ' . $instance->getId() . PHP_EOL;

// Remove the container.
$manager->remove($instance);
```

## API

### Run a container
```php
$manager->run($config)
```
This method runs a container using the create and start methods. It returns the created instance. If the image is not available, it will be downloaded.

### Create a container
```php
$manager->create($config)
```
Create a container. It returns the created instance. The image must be available.

### Start a container
```php
$manager->start($instance)
```
Start a container. The container must be created before.

### Get the status of a container
```php
$manager->getStatus($instance)
```
Get the status of a container. It returns a StatusCase object. Possible values are: `CREATED`, `RUNNING`, `RESTARTING`, `EXITED`, `PAUSED`, `DEAD`.

### Check if a container is running
```php
$manager->isRunning($instance)
```
Check if a container is running. It returns a boolean.

### Check if an image is available
```php
$manager->isImageAvailable($image, $tag)
```
Check if an image is available. It returns a boolean.

### Download an image
```php
$manager->downloadImage($image, $tag)
```
Download an image using the tag.

### Get an instance from its ID
```php
$manager->getInstanceFromId($id)
```
Get an instance object from its ID.

### Remove a container
```php
$manager->remove($instance)
```
Remove a container. This method forces the removal of the container and removes the associated volumes.

## License

This project is licensed under the GPL-3.0 License. See the LICENSE.md file for details.

## Author

Léo Boiron - leo.boiron@gmail.com - https://leoboiron.fr
