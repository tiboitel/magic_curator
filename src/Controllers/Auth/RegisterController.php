<?php

namespace App\Controllers\Auth;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Models\User;
use App\Controllers;

class RegisterController extends \App\Controllers\AbstractController
{
	protected	$renderer;

	/**
	 * RegisterController constructor.
	 *
	 * @param \Slim\Container
	 */
	public function __construct($container)
	{
		parent::__construct($container);
		$this->renderer = $container->renderer;
	}

	public function show(Request $request, Response $response)
	{
		$this->renderer->render($response, 'auth/register.twig', []);
		return $response;
	}

	public function register(Request $request, Response $response)
	{
		$user = User::create([
			'email' => $request->getParam('email'),
			'login' => $request->getParam('login'),
			'password' => password_hash($request->getParam('password'), PASSWORD_DEFAULT)
		]);
		return $response->withRedirect($this->router->pathFor('wantlist.show'));
	}
}
