<?php

namespace App\Controllers\User;

use Slim\Http\Request;
use Slim\Http\Response;
Use App\Models\User;

class UserController extends \App\Controllers\AbstractController
{
	protected	$renderer;
	
	public function __construct($container)
	{
		parent::__construct($container);
		$this->renderer = $container->renderer;
		$this->database = $container->database;
	}

	function show(Request $request, Response $response)
	{
		$user = User::where("email", "jules.boitelle@gmail.com")->first();
		$this->renderer->render($response, "/auth/login.twig", ['user' => $user]);
		return $response;
	}
}
