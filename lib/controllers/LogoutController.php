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

namespace Izejuy\App\Controller\Auth;

use Symfony\Component\HttpFoundation\Response;

/**
 * The logout controller.
 */
class LogoutController
{

    public function index()
    {
        $_SERVER['container']->get('session')->invalidate();
        redirect('/login');
    }
}
