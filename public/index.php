<?php
declare(strict_types=1);
/**
 * Gem.
 *
 * A simple PHP content management system powered by Symfony and CakePHP.
 *
 * @author Gem Contributors <https://github.com/izejuy/gem/graphs/contributors>.
 *
 * @license MIT - A short and simple permissive license with conditions only requiring preservation of copyright and license notices.
 *                Licensed works, modifications, and largerworks may be distributed under different terms and without source code.
 *
 * @link <https://github.com/izejuy/gem/blob/master/LICENSE> MIT License.
 *
 * @link <https://github.com/izejuy/gem> Source.
 */

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

// Require the package dependencies.
require __DIR__ . '/../vendor/autoload.php';

// Require the routes function.
require __DIR__ . '/../routes.php';

// Create the Request object.
$request = Request::createFromGlobals();

// Get the routes.
$routes = getRoutes();

// Add a url matcher.
$matcher = new UrlMatcher($routes, new RequestContext());

$dispatcher = new EventDispatcher();
// ... Add some event listeners.

$dispatcher->addSubscriber(new RouterListener($matcher, new RequestStack()));

// Create your controller and argument resolvers.
$controllerResolver = new ControllerResolver();
$argumentResolver = new ArgumentResolver();

// Instantiate the kernel.
$kernel = new HttpKernel($dispatcher, $controllerResolver, new RequestStack(), $argumentResolver);

// Actually execute the kernel, which turns the request into a response
// by dispatching events, calling a controller, and returning the response.
$response = $kernel->handle($request);

// Send the headers and echo the content.
$response->send();

// Trigger the kernel.terminate event.
$kernel->terminate($request, $response);
