<?php
defined('DS') ?: define('DS', DIRECTORY_SEPARATOR);
defined('ROOT') ?: define('ROOT', dirname(__DIR__) . DS);
/*if (file_exists(ROOT . '.env')) {
 * }*/
return [
	'settings' => [
		'displayErrorDetails'		=> true,
		'addContentLengthHeader'	=> false,
		'renderer' => [
			'template_path' => __DIR__ . '/../templates/'
		],
		'database' => [
			'database_path' => './database/',
			'driver' => 'mysql',
			'host' => 'localhost',
			'database' => 'mtgdataheap',
			'username' => 'mysql',
			'password' => 'password',
			'charset' => 'utf8',
			'collation' => 'utf8_general_ci',
			'prefix' => ''
		]
	]
];
