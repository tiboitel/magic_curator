<?php
$container = $app->getContainer();

$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($container->get('settings')['database']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$container['database'] = function ($c) use ($capsule) {
	return $capsule;
};

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

$container[\App\Controllers\Auth\AuthController::class] = function($c) {
	return new \App\Controllers\Auth\AuthController($c);
};
$container[\App\Controllers\Auth\RegisterController::class] = function($c) {
	return new \App\Controllers\Auth\RegisterController($c);
};

$container[\App\Controllers\Magic\WantlistController::class] = function($c) {
	return new \App\Controllers\Magic\WantlistController($c);
};
$container[\App\Controllers\Magic\DecklistController::class] = function($c) {
	return new \App\Controllers\Magic\DecklistController($c);
};
