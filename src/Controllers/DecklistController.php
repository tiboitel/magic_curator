<?php

namespace App\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;

class DecklistController extends AbstractController
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
		$this->renderer->render($response, "decklist.phtml", ["decklist" => $this->decklist]);
		return $response;
	}

	function load($file)
	{
		return json_decode(file_get_contents($file));
	}
}
