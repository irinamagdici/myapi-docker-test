<?php

namespace Acme\DataBundle\Model\Utility;

use Symfony\Component\HttpFoundation\JsonResponse;

class ApiResponse {

	public static function setResponse($msg, $code = 200) {

		$response = array('message' => $msg);

		return new JsonResponse($response, $code);

	}
}
