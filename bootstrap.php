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
use Symfony\Component\Templating\Helper\SlotsHelper;
use Symfony\Component\Templating\Loader\FilesystemLoader;
use Symfony\Component\Templating\PhpEngine;
use Symfony\Component\Templating\TemplateNameParser;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\ArrayLoader;

// Load env config.
(new Dotenv())->load(__DIR__ . '/gem.env');

// Enable debug if requested by env config.
if ($_SERVER['DEBUG']) {
    Debug::enable();
}

/**
 * Render a template to client-side.
 *
 * @param string $template The template name.
 * @param array  $bindData The bind data for template.
 *
 * @return mixed Returns the pages response.
 */
function render(string $template, array $bindData = [])
{
    $filesystemLoader = new FilesystemLoader(__DIR__ . '/lib/views/%name%');
    $templating = new PhpEngine(new TemplateNameParser(), $filesystemLoader);
    $templating->set(new SlotsHelper());
    return $templating->render($template, $bindData);
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

// The list of avaliable langs.
$langs = [
    'en_US',
    'fr_FR'
];

// Make the get request for the lang change avaliable on all routes.
if (isset($_GET['lang'])) {
    if (!in_array($_GET['lang'], $langs)) {
        goto serverLang;
    }
    
}

/**
 * Return the proper translation based on access point.
 *
 * @param string $accessPoint The access point for a certain translation.
 *
 * @return string The proper translation.
 */
function trans(string $accessPoint = '')
{
    global $translator;
    return $translator->trans($accessPoint)
}

// Construct the container.
$containerBuilder = new ContainerBuilder();

// Database initialization.
$driverOptions = [
    'persistent' => $_SERVER['DB_PERSISTENT'],
    'database'   => $_SERVER['DB_NAME'],
    'username'   => $_SERVER['DB_USER'],
    'password'   => $_SERVER['DB_PASS'],
    'host'       => $_SERVER['DB_HOST'],
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

// Add a mail handler.
$containerBuilder->register('mail', 'Gem\Script\Mail');

// Add a login handler.
$containerBuilder->register('login', 'Gem\Script\Auth\Login')->addArgument([
    'hasher'     => new Reference('hasher'),
    'validator'  => new Reference('validator'),
    'session'    => new Reference('session'),
    'connection' => new reference('connection'),
    'rand'       => new Reference('rand'),
]);

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
