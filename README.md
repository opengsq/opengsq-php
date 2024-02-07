# OpenGSQ PHP Library

[![PHP Composer](https://github.com/opengsq/opengsq-php/actions/workflows/php.yml/badge.svg)](https://github.com/opengsq/opengsq-php/actions/workflows/php.yml)
[![GitHub license](https://img.shields.io/github/license/opengsq/opengsq-php)](https://github.com/opengsq/opengsq-php/blob/main/LICENSE)
[![Packagist Version](https://img.shields.io/packagist/v/opengsq/opengsq-php.svg)](https://packagist.org/packages/opengsq/opengsq-php)
[![Packagist Downloads](https://img.shields.io/packagist/dt/opengsq/opengsq-php.svg)](https://packagist.org/packages/opengsq/opengsq-php)

The OpenGSQ PHP library provides a convenient way to query servers from applications written in the PHP language.

## Documentation

Detailed documentation is available at [https://php.opengsq.com](https://php.opengsq.com).

## System Requirements

- [PHP 8.1.2](https://www.php.net) or higher

## Installation

The recommended way to install the OpenGSQ PHP library is through Composer, a tool for dependency management in PHP. You can install it by running the following command in your terminal:

```sh
composer require opengsq/opengsq-php
```

## Basic Usage

Here’s a quick example of how you can use the OpenGSQ library to query a server using the VCMP protocol:

```php
<?php

// Include the Composer autoloader
require_once '../vendor/autoload.php';

// Import the Vcmp class from the OpenGSQ\Protocols namespace
use OpenGSQ\Protocols\Vcmp;

// Create a new Vcmp object with the specified host and port
$vcmp = new Vcmp('123.123.123.123', 8114);

// Get the status of the server
$status = $vcmp->getStatus();

// Output the status information
var_dump($status);

// Get the players on the server
$players = $vcmp->getPlayers();

// Output the player information
var_dump($players);
```

In this example, we first include the Composer autoloader and import the `Vcmp` class. We then create a new `Vcmp` object, specifying the host and port of the server we want to query. Finally, we call the `getStatus` and `getPlayers` methods to retrieve and output information about the server and its players.

## License

This project is licensed under the MIT License. See the [LICENSE](https://github.com/opengsq/opengsq-php/blob/main/LICENSE) file for details.

## Stargazers over time

[![Stargazers over time](https://starchart.cc/opengsq/opengsq-php.svg?variant=adaptive)](https://starchart.cc/opengsq/opengsq-php)
