<?php
declare(strict_types=1);
/**
 * @author Gem Chess Contributors <https://github.com/izejuy/gem-chess/graphs/contributors>.
 * @link <https://github.com/izejuy/gem-chess> Source.
 */

namespace Gem\Controller\Auth;

use Symfony\Component\HttpFoundation\Response;
use function render;

/**
 * The initial controller for when someone visits
 * the website at the doc root.
 */
class LoginController
{
    /**
     * Return the response.
     *
     * @return mixed The controller response.
     */
    public function view()
    {
        return new Response(render('login.html.php'));
    }
}
