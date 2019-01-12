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

namespace Izejuy\Gem;

/**
 * The rand interface.
 */
interface RandInterface
{

    /**
     * Generate a random bool value.
     *
     * @return bool Returns a random bool value.
     */
    public function getBool(): bool;

    /**
     * Generate a random string value.
     *
     * @param int    $length   The string length.
     * @param string $keyspace The list of characters allowed to use.
     *
     * @return string Returns the generated string.
     */
    public function getString(int $length = 16, string $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'): string;

    /**
     * Generate a random two factor code.
     *
     * @param int $length The code length.
     *
     * @return int Returns the two factor code.
     *
     * @codeCoverageIgnore
     */
    public function getTwoFactorCode(int $length = 6): int;
}
