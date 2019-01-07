<?php
declare(strict_types=1);
/**
 * @author Gem Chess Contributors <https://github.com/izejuy/gem-chess/graphs/contributors>.
 * @link <https://github.com/izejuy/gem-chess> Source.
 */

namespace Gem\Script;

use function ini_set;
use function session_get_cookie_params;
use function session_set_cookie_params;
use function session_start;
use function setcookie;
use function session_regenerate_id;
use function session_destroy;
use function session_name;
use function time;
use const true;
use const null;

/**
 * Preserve session data.
 */
class Session implements SessionInterface
{
    /**
     * Start the session.
     *
     * @return void Returns nothing.
     */
    public static function start()
    {
        session_name($_SERVER['SESSION_NAME']);
        ini_set('session.use_cookies', $_SERVER['SESSION_USE_COOKIES']);
        ini_set('session.use_only_cookies', $_SERVER['SESSION_USE_ONLY_COOKIES']);
        ini_set('session.use_strict_mode', $_SERVER['SESSION_USE_STRICT_MODE']);
        $cookieParams = session_get_cookie_params();
        session_set_cookie_params(
            $cookieParams["lifetime"],
            $cookieParams["path"],
            $cookieParams["domain"],
            $_SERVER['SESSION_SECURE'],
            $_SERVER['SESSION_HTTPONLY']
        );
        session_start();
    }
    
    /**
     * Destroy the session.
     *
     * @return void Returns nothing.
     */
    public static function destroy()
    {
        $_SESSION = array();
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
        session_destroy();
    }

    /**
     * Regenerate the session id.
     *
     * @param bool $deleteOldSession Should we delete the old session.
     *
     * @return bool Returns true on success and false on failure.
     */
    public static function regenerate($deleteOldSession = true)
    {
        return session_regenerate_id($deleteOldSession);
    }
    
    /**
     * Set session data.
     *
     * @param mixed $key   Key that will be used to store value.
     * @param mixed $value Value that will be stored.
     *
     * @return void Returns nothing.
     */
    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Unset session data with provided key.
     *
     * @param $key The session key.
     *
     * @return void Returns nothing.
     */
    public static function delete($key)
    {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }
    
    /**
     * Get data from $_SESSION variable.
     *
     * @param mixed $key     Key used to get data from session.
     * @param mixed $default This will be returned if there is no record inside
     *                       session for given key.
     *
     * @return mixed Session value for given key.
     */
    public static function get($key, $default = null)
    {
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }
        return $default;
    }
}
