<?php
$container = $app->getContainer();

$container['session'] = function ($c) {
	return new Session();
};

$container['flash'] = function ($c) {
	return new \Slim\Flash\Messages();
};

$container['renderer'] = function ($c) {
	$settings = $c->get('settings')['renderer'];
	$view = new \Slim\Views\Twig($settings['template_path'], [
		'cache' => false
	]);

	$view->addExtension(new Slim\Views\TwigExtension(
		$c->router,
		$c->request->getUri()));
	return ($view);
};

$container[\App\Controllers\UserController::class] = function($c) {
	return new \App\Controllers\UserController($c);
};

$container[\App\Controllers\MagicController::class] = function($c) {
	return new \App\Controllers\MagicController($c);
};

$container[\App\Controllers\WantlistController::class] = function($c) {
	return new \App\Controllers\WantlistController($c);
};

$container[\App\Controllers\DecklistController::class] = function($c) {
	return new \App\Controllers\DecklistController($c);
};
