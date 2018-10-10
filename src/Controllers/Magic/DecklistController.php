<?php

namespace App\Controllers\Magic;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Controllers;

class DecklistController extends \App\Controllers\AbstractController
{
	protected	$renderer;
	private		$decklist;
	
	public function __construct($container)
	{
		parent::__construct($container);
		$this->renderer = $container->renderer;
		$this->decklist = $this->load("../database/decklist.json");
	}

	function show(Request $request, Response $response)
	{
		$this->renderer->render($response, "decklist.twig", ["decklist" => $this->decklist]);
		return $response;
	}

	function load($file)
	{
		return json_decode(file_get_contents($file));
	}
}
