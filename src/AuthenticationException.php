<?php

namespace packages\userpanel;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use packages\base\Exception;

class AuthenticationException extends Exception
{

	public function render(Request $request): Response
	{
		if (null !== $qs = $request->getQueryString()) {
			$qs = '?' . $qs;
		}
		$path = $request->getBaseUrl() . $request->getPathInfo();
		if ($qs === null and trim($path, "/") === trim(url(), "/")) {
			$backTo = null;
		} else {
			$backTo = $path . $qs;
		}

		$parameters = [];
		if ($backTo !== null) {
			$parameters['backTo'] = $backTo;
		}
		$loginUrl = url("login", $parameters);
		if ($request->expectsJson()) {
			return response([
				'status' => false,
				'redirect' => $loginUrl
			], 401);
		}
		return response()->redirectTo($loginUrl);
	}
}
