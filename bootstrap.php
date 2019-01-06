<?php
declare(strict_types=1);
/**
 * @author Gem Chess Contributors <https://github.com/izejuy/gem-chess/graphs/contributors>.
 * @link <https://github.com/izejuy/gem-chess> Source.
 */

use Symfony\Component\Debug\Debug;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Templating\PhpEngine;
use Symfony\Component\Templating\TemplateNameParser;
use Symfony\Component\Templating\Loader\FilesystemLoader;

// Load env config.
(new Dotenv())->load(__DIR__ . '/gem.env');

// Enable debug if requested by env config.
if ($_ENV['DEBUG']) {
    Debug::enable();
}

/**
 * Render a template to client-side.
 *
 * @param string $template The template name.
 * @param array  $bindData The bind data for template.
 *
 * @return void Returns nothing.
 */
function render(string $template, array $bindData = []): void
{
    $filesystemLoader = new FilesystemLoader(__DIR__ . '/lib/views/%name%');
    $templating = new PhpEngine(new TemplateNameParser(), $filesystemLoader);
    echo $templating->render($template, $bindData);
}

/**
 * Redirect the user to a different internal page or external.
 *
 * @param string $location The location in which they will go.
 *
 * @return void Returns nothing.
 */
function redirect(string $location = '/'): void
{
    if (!headers_sent()) {
        header("Location: $location");
    } else {
        echo "<script type=\"text/javascript\">";
        echo "window.location.href=\"$loction\";";
        echo "</script>";
        echo "<noscript>";
        echo "<meta http-equiv=\"refresh\" content=\"0;url=$location\">";
        echo "</noscript>";
    }
    exit;
}

// Construct the container.
$containerBuilder = new ContainerBuilder();

// Database initialization.
$driverOptions = [
    'persistent' => $_ENV['DB_PERSISTENT'],
    'database' => $_ENV['DB_NAME'],
    'username' => $_ENV['DB_USER'],
    'password' => $_ENV['DB_PASS'],
    'host' => $_ENV['DB_HOST'],
];

// Apply driver options.
$containerBuilder->setParameter('driver.options', $driverOptions);
$containerBuilder->register('driver', 'Cake\Database\Driver\Mysql')->addArgument('%driver.options%');

// Create a stable connection.
$containerBuilder->register('connection', 'Cake\Database\Connection')->addArgument(['driver' => new Reference('driver')]);

// Add the validator.
$containerBuilder->register('validator', 'Cake\Validation\Validator');

// Add a password hasher handler.
$containerBuilder->register('hasher', 'CrystalChess\Hasher');

// Add a rand generator.
$containerBuilder->register('rand', 'CrystalChess\Rand');

// Add a session handler.
$containerBuilder->register('session', 'Gem\Script\Session');

// Finalize container.
$container = $containerBuilder;

/**
 * Make the container accessible via app function.
 *
 * @param string $service The service to access.
 *
 * @return mixed The service container.
 */
function app(string $service)
{
    global $container;
    return $container->get($service);
}
