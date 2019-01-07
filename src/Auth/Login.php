<?php
declare(strict_types=1);
/**
 * @author Gem Chess Contributors <https://github.com/izejuy/gem-chess/graphs/contributors>.
 * @link <https://github.com/izejuy/gem-chess> Source.
 */

namespace Gem\Script\Auth;

use function is_null;
use function hash_equals;
use function hash;
use function sprintf;
use function date;
use function count;
use function intval;
use function redirect;
use function respond;
use function trans;
use const PHP_INT_MAX;
use const null;
use const false;
use const true;

/**
 * Login auth class.
 */
class Login implements LoginInterface
{
    /**
     * @var mixed $connection The database connection.
     */
    private $connection = null;

    /**
     * @var mixed $session The session handler.
     */
    private $session;

    /**
     * @var mixed $hasher The password hasher.
     */
    private $hasher;

    /**
     * @var mixed $rand The rand generator.
     */
    private $rand;

    /**
     * @var mixed $mail The mail handler.
     */
    private $mail;

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
        $this->session    = $data['session'];
        $this->hasher     = $data['hasher'];
        $this->rand       = $data['rand'];
        $this->mail       = $data['mail'];
    }

    /**
     * Is the user logged in.
     *
     * @return bool Returns true if the user is logged in and false
     *              if otherwise.
     */
    public static function isLoggedIn(): bool
    {
        if (is_null($this->session->get('user_id'))) {
            return false;
        } elseif ($_SERVER['LOGIN_FINGERPRINT']) {
            $loginString = $this->generateLoginString();
            $currentString = $this->session->get('login_fingerprint');
            if (!is_null($currentString) && hash_equals($currentString, $loginString)) {
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
    public function loginById($id): void
    {
        if ($id < 1 && $id != '' && !is_null($id)) {
            $this->updateLoginDate($id);
            $this->session->set('user_id', $id);
            if ($_SERVER['LOGIN_FINGERPRINT'] == true) {
                $this->session->set('login_fingerprint', $this->generateLoginString());
            }
        }
    }

    /**
     */
    public function userLogin(string $username, string $password): void
    {
        if ($this->isBruteForce()) {
            return respond([
                'status'  => 'error',
                'message' => trans('brute.force')
            ]);
        }
        $statement = $connection->prepare('SELECT * FROM users WHERE username = :user');
        $statement->bind(['user' => $username], ['user' => 'string']);
        $results = $statement->fetchAll('assoc');
        if (count($results) !== 1) {
            $this->increaseLoginAttempts();
            return respond([
                'status'  => 'error',
                'message' => trans('user.not.found')
            ]);
        }
        if ($results[0]['confirmed'] == 'N') {
            $this->increaseLoginAttempts();
            return respond([
                'status'  => 'error',
                'message' => trans('user.not.confirmed')
            ]);
        }
        if ($results[0]['banned'] == 'Y') {
            $this->increaseLoginAttempts();
            return respond([
                'status'  => 'error',
                'message' => trans('user.banned')
            ]);
        }
        if (!$this->hasher->verify($password, $results[0]['password']) {
            $this->increaseLoginAttempts();
            return respond([
                'status'  => 'error',
                'message' => trans('password.incorrect')
            ]);
        }
        if ($results[0]['two_factor_auth'] == 'Y') {
            $twoFactorCode = $this->rand->getTwoFactorCode();
            $this->session->set('two_factor_code', $twoFactorCode);
            $this->mail->send($results[0]['mail'], 'two.factor.mail.tpl', ['twoFactorCode' => $twoFactorCode]);
            redirect('/2fa');
        }
        $this->updateLoginDate($result[0]['user_id']);
        $this->session->set('user_id', $result[0]['user_id']);
        $this->session->regenerate();
        if ($_SERVER['LOGIN_FINGERPRINT'] == true) {
            $this->session->set('login_fingerprint', $this->generateLoginString());
        }
        redirect('/dashboard');
    }

    /**
     * Increase login attempts from specific IP address to preven brute force attack.
     *
     * @return void Returns nothing.
     */
    public function increaseLoginAttempts()
    {
        $date   = date("Y-m-d");
        $userIp = $_SERVER['REMOTE_ADDR'];
        $table  = 'login_attempts';
        $loginAttempts = $this->getLoginAttempts();
        if ($loginAttempts > 0) {
            $loginAttempts = $loginAttempts + 1;
            $this->connection->update($table, [
                'attempt_number' => $loginAttempts
            ], [
                'ip_addr' => $userIp,
                'date'    => $date
            ], [
                'attempt_number' => 'integer',
                'ip_addr'        => 'string',
                'date'           => 'date'
            ]);
        } else {
            $connection->insert($table, [
                'ip_addr' => $userIp,
                'date'    => $date
            ], [
                'ip_addr' => 'string',
                'date'    => 'date'
            ]);
        }
    }
    
    /**
     * Log out user and destroy session.
     *
     * @return void Returns nothing.
     */
    public function logout()
    {
        redirect('/logout');
    }

    /**
     * Check if someone is trying to break password with brute force attack.
     *
     * @return bool Returns true if number of attempts are greater than allowed, false otherwise.
     */
    public function isBruteForce()
    {
        return $this->getLoginAttempts() > $_SERVER['LOGIN_MAX_LOGIN_ATTEMPTS'];
    }

    /**
     * Get the current number of login attempts.
     *
     * @return int The login attempt number.
     */
    private function getLoginAttempts()
    {
        $date   = date("Y-m-d");
        $userIp = $_SERVER['REMOTE_ADDR'];
        if (!$userIp) {
            return PHP_INT_MAX;
        }
        $statement = $this->connection->prepare('SELECT * FROM login_attempts WHERE ip_addr = :ip AND date = :date');
        $statement->bind(['ip' => $userIp, 'date' => $date], ['ip' => 'string', 'date' => 'date']);
        $results = $statement->fetchAll('assoc');
        if (count($results) == 0) {
            return 0;
        }
        return intval($results[0]['attempt_number']);
    }
    
    /**
     * Update database with login date and time when this user is logged in.
     *
     * @param int $userId Id of user that is logged in.
     *
     * @return void Returns nothing.
     */
    private function updateLoginDate($userId)
    {
        $this->connection->update('users', [
            'last_login' => date("Y-m-d H:i:s")
        ], [
            'user_id' => intval($userId)
        ], [
            'last_login' => 'date',
            'user_id'    => 'integer'
        ]);
    }
}
