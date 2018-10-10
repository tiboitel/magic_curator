<?php

use Slim\Http\Request;
use Slim\Http\Response;

$app->group('', 
	function() 
	{
		$this->get('/', \App\Controllers\Magic\WantlistController::class . ':show')->setName('wantlist.show');
		$this->get('/{format}/decklist/', \App\Controllers\Magic\DecklistController::class . ':show')->setName('decklist.show');
	})->add(new App\Middleware\AuthentificationMiddleware());
$app->get('/auth/register', \App\Controllers\Auth\RegisterController::class . ':show')->setName('register.show');
$app->post('/auth/register', \App\Controllers\Auth\RegisterController::class . ':register')->setname('register.register');
