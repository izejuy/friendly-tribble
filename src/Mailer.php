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

use Symfony\Component\OptionsResolver\OptionsResolver;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

use function file_get_contents;
use function escape;

use const true;
use const false;

/**
 * The mailer class.
 */
class Mailer implements MailerInterface
{

    /** @var $options The mailer options. */
    private $options = [];

    /** @var $instance The mailer instance. */
    private $instance;

    /**
     * Create a new mailer instance.
     *
     * @param array $options The mailer options.
     *
     * @return void Returns nothing.
     */
    public function __construct(array $options = [])
    {
        $this->setOptions($options);
        $this->instance = new PHPMailer(true);
        $this->constructMailerGateway();
    }

    /**
     * Set the mailer options.
     *
     * @param array $options The mailer options.
     *
     * @return self Returns this class.
     */
    public function setOptions(array $options = []): MailerInterface
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->options = $resolver->resolve($options);
        return $this;
    }

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
    public function send(array $to, string $subject, array $bindings = [], string $template = 'default.tpl'): void
    {
        try {
            $mail->addAddress($to['address'], $to['name']);
            $templateDir      = __DIR__ . '/../templates/email';
            $templateContents = file_get_contents($templateDir . $template);
            foreach ($bindings as $key => $binding) {
                $templateContents = str_replace('{{' . $key . '}}', $binding, $templateContents);
            }
            $this->instance->isHTML(true);
            $mail->Subject = escape($subject);
            $mail->Body    = escape($templateContents);
            $mail->send();
        } catch (Exception $e) {
            throw new RuntimeException('The email could not be sent.');
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
            'smtp_enabled'  => false,
            'smtp_host'     => '',
            'smtp_auth'     => false,
            'smtp_user'     => '',
            'smtp_pass'     => '',
            'smtp_secure'   => 'tls',
            'smtp_port'     => 587,
            'from'          => 'user@example.com',
            'from_name'     => 'User',
            'reply_to'      => 'reply.user@example.com',
            'reply_to_name' => 'User',
        ]);
    }

    /**
     * Construct the mailer gateway.
     *
     * @return void Returns nothing.
     */
    private function constructMailerGateway(): void
    {
        if ($options['smtp_enabled']) {
            $this->instance->isSMTP();
            $this->instance->Host       = $options['smtp_host'];
            $this->instance->SMTPAuth   = $options['smtp_auth'];
            $this->instance->Username   = $options['smtp_user'];
            $this->instance->Password   = $options['smtp_pass'];
            $this->instance->SMTPSecure = $options['smtp_secure'];
            $this->instance->Port       = $options['smtp_port'];     
        }
        $this->instance->setFrom($options['from'], $options['from_name']);
        $this->instance->addReplyTo($options['reply_to'], $options['reply_to_name']);
    }
}
