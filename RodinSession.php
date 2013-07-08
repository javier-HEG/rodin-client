<?php

include_once('RodinBroker.php');

class RodinSession {

	const SESSION_USER_NAME = 'username';
	const SESSION_REAL_NAME = 'userrealname';
	const SESSION_PASSWORD = 'password';
	const SESSION_ATTEMPTS = 'attempts';
	const SESSION_LAST_ATTEMPT = 'lastattempt';
	const SESSION_TIMEOUT = 'timeout';
	const SESSION_UNIVERSE = 'universename';
	const SESSION_UNIVERSE_ID = 'universeid';

	public function __construct() {
		session_name('rodinclient');
		session_start();

		// Set time-out period (in seconds)
		$inactive = 3600;
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

	public function getUserName() {
		return $_SESSION[RodinSession::SESSION_USER_NAME];
	}

	public function getUserRealName() {
		return $_SESSION[RodinSession::SESSION_REAL_NAME];
	}

	public function getUniverseName() {
		return $_SESSION[RodinSession::SESSION_UNIVERSE];
	}

	public function getUniverseId() {
		return $_SESSION[RodinSession::SESSION_UNIVERSE_ID];
	}

	public function userLogout() {
		session_destroy();
		header('Location: index.php');
	}

	public function userLoginAttempt($username, $password) {
		$response = RodinBroker::makeCallToServer(RodinBroker::METHOD_GET, 'user/' . $username);

		if ($response->code == 200 && $userPassword = $response->body->password) {
			if ($password == $userPassword) {
				$_SESSION[RodinSession::SESSION_USER_NAME] = $username;
				$_SESSION[RodinSession::SESSION_REAL_NAME] = $response->body->name;

				$_SESSION[RodinSession::SESSION_UNIVERSE_ID] = $response->body->universeid;

				// FIXME Remove this test once the creation of a user cascades
				// into the creation of a default universe and the last universe
				// can not be deleted
				if ($_SESSION[RodinSession::SESSION_UNIVERSE_ID] == false) {
					$_SESSION[RodinSession::SESSION_UNIVERSE] = "(No universe)";
				} else {
					$universeResponse = RodinBroker::makeCallToServer(RodinBroker::METHOD_GET, 'universe/' . $_SESSION[RodinSession::SESSION_UNIVERSE_ID]);
					$_SESSION[RodinSession::SESSION_UNIVERSE] = $universeResponse->body->name;
				}

				$logMessage = '';
				foreach ($response->headers->toArray() as $key => $value) {
					if (strpos($key, 'access-control') !== false) {
						$logMessage .= $key . ':' . $value . '<br />';
					}
				}

				setInterfaceMessage(MESSAGE_KIND_DEBUG, $logMessage);

				$this->resetLogginAttempts();
				session_regenerate_id();

				return true;
			}
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
			return $_SESSION[RodinSession::SESSION_ATTEMPTS]; else {
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
