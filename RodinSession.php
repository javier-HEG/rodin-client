<?php

class RodinSession {

	public function __construct() {
		session_name('rodinclient');
		session_start();

		// Set time-out period (in seconds)
		$inactive = 600;
		// Check to see if $_SESSION["timeout"] is set
		if (isset($_SESSION['timeout'])) {
			// calculate the session's "time to live"
			$sessionTTL = time() - $_SESSION['timeout'];
			if ($sessionTTL > $inactive) {
				session_destroy();
				header('Location: index.php');
			}
		}

		$_SESSION['timeout'] = time();
	}

	/**
	 * Checks if a user is already logged in
	 * @return boolean
	 */
	public function isUserLoggedIn() {
		return isset($_SESSION['username']);
	}

	public function userLogout() {
		unset($_SESSION['username']);
		header('Location: index.php');
	}

	public function newUserLogin($username, $password) {
		// TODO Get user information from server and compare
		if ($username == 'username' && $password == sha1('username')) {
			$_SESSION['username'] = $username;
			session_regenerate_id();
			return true;
		} else {
			return false;
		}
	}

}
