<?php

namespace App\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;
use mtgsdk\Card;

class MagicController extends AbstractController
{
	function show(Request $request, Response $response)
	{	
		return $response;
	}
}
