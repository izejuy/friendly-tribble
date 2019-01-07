<?php
declare(strict_types=1);
/**
 * @author Gem Chess Contributors <https://github.com/izejuy/gem-chess/graphs/contributors>.
 * @link <https://github.com/izejuy/gem-chess> Source.
 */

namespace Gem\Script\Auth;

use function hash;
use const null;
use const false;
use const true;

/**
 * Login auth class.
 */
class Login implements LoginInterface
{
    /**
     * @var mixed $rand The rand generator.
     */
    private $rand;

    /**
     * @var mixed $session The session handler.
     */
    private $session;

    /**
     * @var mixed $validator The validator.
     */
    private $validator;

    /**
     * @var mixed $connection The database connection.
     */
    private $connection = null;

    /**
     * @var mixed $hasher The password hasher.
     */
    private $hasher;

    /**
     * Inject classes.
     *
     * @param array $data Contains other classes injected.
     *
     * @return void Returns nothing.
     */
    public function __construct(array $data = [])
    {
        $this->connection = $data['connection'];
        $this->validator  = $data['validator'];
        $this->session    = $data['session'];
        $this->hasher     = $data['hasher'];
        $this->rand       = $data['rand'];
    }

    /**
     * Is the user logged in.
     *
     * @return bool Returns true if the user is logged in and false
     *              if otherwise.
     */
    public static function isLoggedIn(): bool
    {
        if ($this->session->get('user_id') == null) {
            return false;
        } elseif ($_SERVER['LOGIN_FINGERPRINT']) {
            $loginString = $this->generateLoginString();
            $currentString = $this->session->get('login_fingerprint');
            if ($currentString != null && $currentString !== $loginString) {
                $this->logout();
                return false;
            }
        }
        return true;
    }

    /**
     * Generate string that will be used as fingerprint.
     * This is actually string created from user's browser name and user's IP
     * address, so if someone steal users session, he won't be able to access.
     *
     * @return string Generated string.
     */
    private function generateLoginString(): string
    {
        $fingerprint = sprintf(
            "%s|%s",
            $_SERVER['REMOTE_ADDR'],
            $_SERVER['HTTP_USER_AGENT'],
            $this->rand->getString(10),
        );
        return hash('sha512', $fingerprint);
    }

    /**
     * Log in user with provided id.
     *
     * @param $id The account id to log user into.
     *
     * @return void Returns nothing.
     */
    public function byId($id): void
    {
        if ($id != 0 && $id != '' && $id != null) {
            $this->updateLoginDate($id);
            $session->set('user_id', $id);
            if ($_SERVER['LOGIN_FINGERPRINT'] == true) {
                $session->set("login_fingerprint", $this->generateLoginString());
            }
        }
    }
}
