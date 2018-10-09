<?php

namespace App\Controllers\User;

use Slim\Http\Request;
use Slim\Http\Response;

class UserController extends AbstractController
{
	protected	$renderer;
	
	public function __construct($container)
	{
		parent::__construct($container);
		$this->renderer = $container->renderer;
	}

	function show(Request $request, Response $response)
	{
		var_dump($request);
		$this->renderer->render($response, "index.phtml", []);
		return $response;
	}
}
