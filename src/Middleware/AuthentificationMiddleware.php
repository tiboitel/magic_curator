<?php

namespace App\Middleware;

use Slim\Http\Request;
use Slim\Http\Response;

class AuthentificationMiddleware
{
	protected $userMapper;
	
	public function __construct()
	{
	}

	public function __invoke(Request $request, Response $response, callable $next)
	{
		$next($request, $response);
		return $response;
	}

	public function generateToken() : ?string
	{
		$token = uniqid("login_", true);
		return $token;
	}
}
