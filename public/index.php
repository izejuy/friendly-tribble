<?php
declare(strict_types=1);
/**
 * Gem Chess - A better website for playing chess
 *
 * @author Gem Chess Contributors <https://github.com/izejuy/gem-chess/graphs/contributors>.
 *
 * @link <https://github.com/izejuy/gem-chess> Source.
 */

use Symfony\Component\Debug\Debug;
use Symfony\Component\Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';

(new Dotenv())->load(__DIR__ . '/gem.env');

if ($_ENV['DEBUG']) {
    Debug::enable();
}
