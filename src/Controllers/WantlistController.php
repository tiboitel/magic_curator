<?php

namespace App\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;
use mtgsdk\Card;

class WantlistController extends AbstractController
{
	protected	$renderer;
	private		$dataset;
	
	public function __construct($container)
	{
		parent::__construct($container);
		$this->renderer = $container->renderer;
		$this->dataset = $this->load("../database/database.csv");
	}

	function show(Request $request, Response $response)
	{
		$wantlist = $this->generate(["price_min" => 0.11, "price_max" => 0.70, "usage_min" => 5,
		"usage_max" => 9999999]);
		$this->renderer->render($response, 'index.phtml', array("wantlist" => $wantlist));
		return $response;
	}

	function load($file)
	{
		$dataset = [];
		$file_handler = fopen($file, "r");
		while (($data = fgetcsv($file_handler, 1024, ";")) !== FALSE)
		{
			$dataset[] = [
			"name" => $data[0],
			"usage" => $data[1],
			"price_low" => $data[2],
			"price_average" => $data[3],
			"price_high" => $data[4]
			];
		}
		fclose($file_handler);
		return ($dataset);
	}

	function generate($settings)
	{
		$wantlist = "";
		foreach ($this->dataset as $card)
		{
			if ($card['price_low'] >= $settings['price_min'] &&
				$card['price_low'] <= $settings['price_max'] &&
					$card['usage'] >= $settings['usage_min'] &&
						$card['usage'] <= $settings['usage_max'] &&
							$card['price_low'] != 0.0)
			{
				if (strpos($wantlist, $card['name']) == FALSE)
					$wantlist .= "4x " . $card['name'] . "\r\n";
			}
		}
		return ($wantlist);
	}
}
