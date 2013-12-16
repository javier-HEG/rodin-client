<?php

require 'lib/httpful/bootstrap.php';

use \Httpful\Request;

/**
 * Can be used statically to communicate with the server
 */
class RodinBroker {

	const RODIN_SERVER = 'http://127.0.0.1:8080/rodin_server/resources/';
	const METHOD_GET = 0;
	const METHOD_POST = 1;
	const METHOD_PUT = 2;
	const METHOD_DELETE = 3;

	public static function makeCallToServer($method = RodinBroker::METHOD_GET, $resource, $parameters = null) {
		if ($parameters == null)
			return RodinBroker::makeCall($method, RodinBroker::RODIN_SERVER . $resource);
		else
			return RodinBroker::makeCall($method, RodinBroker::RODIN_SERVER . $resource, $parameters);
	}

	public static function makeCall($method = RodinBroker::METHOD_GET, $url, $parameters = null, $proxy = null) {
		switch ($method) {
			case RodinBroker::METHOD_GET:
				if ($proxy == null) {
					$request = \Httpful\Request::get($url, 'application/json');
					$response = $request->send();
					return $response;
				} else {
					$request = \Httpful\Request::get($url, 'application/json')->useProxy($proxy['host'], intval($proxy['port']),
						CURLAUTH_BASIC, $proxy['user'], $proxy['password']);
					$response = $request->send();
					return $response;
				}
			case RodinBroker::METHOD_POST:
			case RodinBroker::METHOD_PUT:
			case RodinBroker::METHOD_DELETE:
			default:
				break;
		}
	}

}

?>
