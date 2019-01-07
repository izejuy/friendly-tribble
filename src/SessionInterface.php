<?php
declare(strict_types=1);
/**
 * @author Gem Chess Contributors <https://github.com/izejuy/gem-chess/graphs/contributors>.
 * @link <https://github.com/izejuy/gem-chess> Source.
 */

namespace Gem\Script;

/**
 * Interface for session handler.
 */
interface SessionInterface
{
    /**
     * Start the session.
     *
     * @return void Returns nothing.
     */
    public static function start();
    
    /**
     * Destroy the session.
     *
     * @return void Returns nothing.
     */
    public static function destroy();

    /**
     * Regenerate the session id.
     *
     * @param bool $deleteOldSession Should we delete the old session.
     *
     * @return bool Returns true on success and false on failure.
     */
    public static function regenerate($deleteOldSession = true);
    
    /**
     * Set session data.
     *
     * @param mixed $key   Key that will be used to store value.
     * @param mixed $value Value that will be stored.
     *
     * @return void Returns nothing.
     */
    public static function set($key, $value);

    /**
     * Unset session data with provided key.
     *
     * @param $key The session key.
     *
     * @return void Returns nothing.
     */
    public static function delete($key);
    
    /**
     * Get data from $_SESSION variable.
     *
     * @param mixed $key     Key used to get data from session.
     * @param mixed $default This will be returned if there is no record inside
     *                       session for given key.
     *
     * @return mixed Session value for given key.
     */
    public static function get($key, $default = null);
}
