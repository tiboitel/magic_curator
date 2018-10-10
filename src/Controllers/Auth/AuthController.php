<?php

namespace App\Controllers\Auth;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Models\User;

class AuthController extends AbstractController
{
	protected	$renderer;

	/**
	 * AuthController constructor.
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
	}

	public function register(Request $request, Response $response)
	{
		$this->renderer->render($response, 'auth/register.twig', []);
		return $response;
	}
}
