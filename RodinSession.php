<?php

class RodinSession {

	const SESSION_USER_NAME = 'username';
	const SESSION_REAL_NAME = 'userrealname';
	const SESSION_PASSWORD = 'password';
	const SESSION_ATTEMPTS = 'attempts';
	const SESSION_LAST_ATTEMPT = 'lastattempt';
	const SESSION_TIMEOUT = 'timeout';

	public function __construct() {
		session_name('rodinclient');
		session_start();

		// Set time-out period (in seconds)
		$inactive = 600;
		// Check to see if $_SESSION["timeout"] is set
		if (isset($_SESSION[RodinSession::SESSION_TIMEOUT])) {
			// calculate the session's "time to live"
			$sessionTTL = time() - $_SESSION[RodinSession::SESSION_TIMEOUT];
			if ($sessionTTL > $inactive) {
				session_destroy();
				header('Location: index.php');
			}
		}

		$_SESSION[RodinSession::SESSION_TIMEOUT] = time();
	}

	/**
	 * Checks if a user is already logged in
	 * @return boolean
	 */
	public function isUserLoggedIn() {
		return isset($_SESSION[RodinSession::SESSION_USER_NAME]);
	}

	public function getUserRealName() {
		return $_SESSION[RodinSession::SESSION_REAL_NAME];
	}

	public function userLogout() {
		session_destroy();
		header('Location: index.php');
	}

	public function userLoginAttempt($username, $password) {
		// TODO Get user information from server and compare
		if ($username == 'username' && $password == sha1('username')) {
			$_SESSION[RodinSession::SESSION_USER_NAME] = $username;
			$_SESSION[RodinSession::SESSION_REAL_NAME] = 'Test User';
			$this->resetLogginAttempts();
			session_regenerate_id();
			return true;
		} else {
			$this->registerLoginAttempt();
			return false;
		}
	}

	private function registerLoginAttempt() {
		if (isset($_SESSION[RodinSession::SESSION_ATTEMPTS])) {
			$_SESSION[RodinSession::SESSION_ATTEMPTS] += 1;
			$_SESSION[RodinSession::SESSION_LAST_ATTEMPT] = time();
		} else {
			$_SESSION[RodinSession::SESSION_ATTEMPTS] = 1;
			$_SESSION[RodinSession::SESSION_LAST_ATTEMPT] = time();
		}
	}

	public function getUserLoginAttempts() {
		if (isset($_SESSION[RodinSession::SESSION_ATTEMPTS]))
			return $_SESSION[RodinSession::SESSION_ATTEMPTS];
		else {
			if ($this->isUserLoggedIn())
				return -1;
			else
				return 0;
		}
	}

	public function timeSinceLastAttempt() {
		if (isset($_SESSION[RodinSession::SESSION_LAST_ATTEMPT]))
			return time() - $_SESSION[RodinSession::SESSION_LAST_ATTEMPT];
		else
			return 0;
	}

	public function resetLogginAttempts() {
		unset($_SESSION[RodinSession::SESSION_ATTEMPTS]);
		unset($_SESSION[RodinSession::SESSION_LAST_ATTEMPT]);
	}

}
