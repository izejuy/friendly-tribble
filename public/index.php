<?php
declare(strict_types=1);
/**
 * @author Gem Chess Contributors <https://github.com/izejuy/gem-chess/graphs/contributors>.
 * @link <https://github.com/izejuy/gem-chess> Source.
 */

use Symfony\Component\Debug\Debug;
use Symfony\Component\Dotenv\Dotenv;

// Require the package dependencies.
require_once __DIR__ . '/../vendor/autoload.php';

// Load env config.
(new Dotenv())->load(__DIR__ . '/../gem.env');

// Enable debug if requested by env config.
if ($_ENV['DEBUG']) {
    Debug::enable();
}

// Require the bootstrap file for additional code.
require_once __DIR__ . '/../bootstrap.php';



