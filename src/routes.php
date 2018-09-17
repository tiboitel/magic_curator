<?php

use Slim\Http\Request;
use Slim\Http\Response;

$app->group('', 
	function() 
	{
		$this->get('/', \App\Controllers\MagicController::class . ':show')->setName('magic.show');
	}
);
