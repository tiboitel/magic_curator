<?php

use Slim\Http\Request;
use Slim\Http\Response;

$app->group('', 
	function() 
	{
		$this->get('/', \App\Controllers\WantlistController::class . ':show')->setName('wantlist.show');
		$this->get('/{format}/decklist/', \App\Controllers\DecklistController::class . ':show')->setName('decklist.show');
	}
);
