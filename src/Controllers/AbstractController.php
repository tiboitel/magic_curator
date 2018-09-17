<?php
namespace App\Controllers;

use Interop\Container\ContainerInterface;

class AbstractController
{
	protected $container;

	public function __construct(ContainerInterface $container)
	{
		$this->container = $container;
	}
}
