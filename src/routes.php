<?php

use Slim\Http\Request;
use Slim\Http\Response;

$app->group('', 
	function() 
	{
		$this->get('/', \App\Controllers\WantlistController::class . ':show')->setName('wantlist.show');
	}
);
