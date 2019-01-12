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
 * The hasher interface.
 */
interface HasherInterface
{

    /**
     * Create a new hasher instance.
     *
     * @param array $options The hasher options.
     *
     * @return void Returns nothing.
     */
    public function __construct(array $options = []);

    /**
     * Set the hasher options.
     *
     * @param array $options The hasher options.
     *
     * @return self Returns this class.
     */
    public function setOptions(array $options = []): self;

    /**
     * Create a hash.
     *
     * @param string $text The text to hash.
     *
     * @return string The hashed text.
     */
    public function create(string $text): string;

    /**
     * Verify the text matches the hash.
     *
     * @param string $text The text to check.
     * @param string $hash the hash to check against.
     *
     * @return bool Returns true if the match and false if not.
     */
    public function verify(string $text, string $hash): bool;

    /**
     * Check to see if the hash needs a rehash.
     *
     * @param string $hash The hash to check.
     *
     * @return bool Returns true if the hash needs a rehash and false if not.
     */
    public function needsRehash(string $hash): bool;
}
