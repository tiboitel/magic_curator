<?php

namespace App\Middleware;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Mapper\UserMapper as UserMapper;

class AuthentificationMiddleware
{
	protected $userMapper;
	
	public function __construct()
	{
		$this->userMapper = new UserMapper("../database/passwd.txt");
	}

	public function __invoke(Request $request, Response $response, callable $next)
	{
		$login = $request->getAttribute('login');
		$password = $request->getAttribute('password');
		if (!empty($login) && !empty($password) && $_SESSION['is_authenticated'] === false)
		{
			if (($user = $this->userMapper->findByLogin($login)) === NULL)
			{
				$this->flash->addMessage("error", "Username is invalid or doesn't exist !");
				$response->withStatus(401);
			}
			if ($user['password'] === password_hash($password, PASSWORD_BCRYPT))
			{
				$user = new User();
				$user->setLogin($login);
				$user->setToken($this->generateToken());
				$token_date = new DateTime();
				$token_date->add("P1D");
				$user->setLogged(true);
				$user->setTokenDateExpired($token_date);
				$request->withAttribute("Login", $user->getLogin());
				$request->withAttribute("Token", $user->setToken());
				$request->withAttribute("TokenDateExpired", $user->getTokenDateExpired());
			}
		}
		$next($request, $response);
		return $response;
	}

	public function generateToken() : ?string
	{
		$token = uniqid("login_", true);
		return $token;
	}
}
