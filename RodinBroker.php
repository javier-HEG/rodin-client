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

	public static function makeCallToServer($method = RodinBroker::METHOD_GET, $resource, $parameters = NULL) {
		switch ($method) {
			case RodinBroker::METHOD_POST:
				break;
			case RodinBroker::METHOD_PUT:
				break;
			case RodinBroker::METHOD_DELETE:
				break;
			case RodinBroker::METHOD_GET:
			default:
				$request = \Httpful\Request::get(RodinBroker::RODIN_SERVER . $resource)
						->mime('application/json');
				$response = $request->send();

				return $response;
		}
	}

}

?>
