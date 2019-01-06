<?php
declare(strict_types=1);
/**
 * @author Gem Chess Contributors <https://github.com/izejuy/gem-chess/graphs/contributors>.
 * @link <https://github.com/izejuy/gem-chess> Source.
 */

use Symfony\Component\Templating\PhpEngine;
use Symfony\Component\Templating\TemplateNameParser;
use Symfony\Component\Templating\Loader\FilesystemLoader;

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
