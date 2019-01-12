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

use Illuminate\Hashing\Argon2IdHasher;
use Illuminate\Hashing\ArgonHasher;
use Illuminate\Hashing\BcryptHasher;
use Symfony\Component\OptionsResolver\OptionsResolver;
use UnexpectedValueException;

use const PASSWORD_ARGON2_DEFAULT_MEMORY_COST;
use const PASSWORD_ARGON2_DEFAULT_THREADS;
use const PASSWORD_ARGON2_DEFAULT_TIME_COST;

/**
 * The hasher.
 */
class Hasher implements HasherInterface
{

    /** @var $options The hasher options. */
    private $options = [];

    /** @var $instance The hasher instance. */
    private $instance;

    /**
     * Create a new hasher instance.
     *
     * @param array $options The hasher options.
     *
     * @return void Returns nothing.
     */
    public function __construct(array $options = [])
    {
        $this->setOptions($options);
        $this->instance = $this->getHasherInstance();
    }

    /**
     * Set the hasher options.
     *
     * @param array $options The hasher options.
     *
     * @return self Returns this class.
     */
    public function setOptions(array $options = []): HasherInterface
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->options = $resolver->resolve($options);
        return $this;
    }

    /**
     * Create a hash.
     *
     * @param string $text The text to hash.
     *
     * @return string The hashed text.
     */
    public function create(string $text): string
    {
        return $this->instance->make($text);
    }

    /**
     * Verify the text matches the hash.
     *
     * @param string $text The text to check.
     * @param string $hash the hash to check against.
     *
     * @return bool Returns true if the match and false if not.
     */
    public function verify(string $text, string $hash): bool
    {
        return $this->instance->check($text, $hash);
    }

    /**
     * Check to see if the hash needs a rehash.
     *
     * @param string $hash The hash to check.
     *
     * @return bool Returns true if the hash needs a rehash and false if not.
     */
    public function needsRehash(string $hash): bool
    {
        return $this->instance->needsRehash($hash);
    }

    /**
     * Get the hasher instance.
     *
     * @throws UnexpectedValueException If the algo could not be determined.
     *
     * @return mixed The hasher instance.
     */
    private function getHasherInstance()
    {
        if ($this->options['algo'] == 'bcrypt') {
            return new BcryptHasher([
                'rounds' => $this->options['cost'],
                'verify' => true,
            ]);
        } elseif ($this->options['algo'] == 'argon2i') {
            // @codeCoverageIgnoreStart
            return new ArgonHasher([
                'memory_cost' => $this->options['memory_cost'],
                'time_cost'   => $this->options['time_cost'],
                'threads'     => $this->options['threads'],
                'verify'      => true,
            ]);
            // @codeCoverageIgnoreEnd
        } elseif ($this->options['algo'] == 'argon2id') {
            // @codeCoverageIgnoreStart
            return new Argon2IdHasher([
                'memory_cost' => $this->options['memory_cost'],
                'time_cost'   => $this->options['time_cost'],
                'threads'     => $this->options['threads'],
                'verify'      => true,
            ]);
            // @codeCoverageIgnoreEnd
        } else {
            throw new UnexpectedValueException('Could not determine the hash algo.');
        }
    }

    /**
     * Configure the options.
     *
     * @param OptionsResolver The symfony options resolver.
     *
     * @return void Returns nothing.
     */
    private function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'algo'        => 'bcrypt',
            'cost'        => 10,
            'memory_cost' => PASSWORD_ARGON2_DEFAULT_MEMORY_COST,
            'time_cost'   => PASSWORD_ARGON2_DEFAULT_TIME_COST,
            'threads'     => PASSWORD_ARGON2_DEFAULT_THREADS,
        ]);
    }
}
