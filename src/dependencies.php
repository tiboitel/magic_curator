<?php
$container = $app->getContainer();

$container['renderer'] = function ($c) {
	$settings = $c->get('settings')['renderer'];
	return new \Slim\Views\PhpRenderer($settings['template_path']);
};

$container[\App\Controllers\MagicController::class] = function($c) {
	return new \App\Controllers\MagicController($c);
};

$container[\App\Controllers\WantlistController::class] = function($c) {
	return new \App\Controllers\WantlistController($c);
};
