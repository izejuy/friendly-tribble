<?php
declare(strict_types=1);
/**
 * @author Gem Chess Contributors <https://github.com/izejuy/gem-chess/graphs/contributors>.
 * @link <https://github.com/izejuy/gem-chess> Source.
 */

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\YamlFileLoader;

// Load the route yaml config file.
$fileLocator = new FileLocator(array(__DIR__));
$loader = new YamlFileLoader($fileLocator);
$routes = $loader->load('routes.yml');
