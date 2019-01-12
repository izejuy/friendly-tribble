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
 * The mailer interface.
 */
interface MailerInterface
{

    /**
     * Create a new mailer instance.
     *
     * @param array $options The mailer options.
     *
     * @return void Returns nothing.
     */
    public function __construct(array $options = []);

    /**
     * Set the mailer options.
     *
     * @param array $options The mailer options.
     *
     * @return self Returns this class.
     */
    public function setOptions(array $options = []): self;

    /**
     * Send an email.
     *
     * @param array  $to       Where the email is going.
     * @param string $subject  The email subject.
     * @param array  $bindings The email bindings.
     * @param string $template The template name to use.
     *
     * @throws RuntimeException If the email could not bee sent.
     *
     * @return void Returns nothing.
     */
    public function send(array $to, string $subject, array $bindings = [], string $template = 'default'): void;
}
