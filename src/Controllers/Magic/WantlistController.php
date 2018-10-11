<?php

namespace App\Controllers\Magic;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Controllers;
use mtgsdk\Card;

class WantlistController extends \App\Controllers\AbstractController
{
	protected	$renderer;
	private		$dataset;
	
	public function __construct($container)
	{
		parent::__construct($container);
		$this->renderer = $container->renderer;
		$this->dataset = $this->load("../database/database.csv");
	}

	public function show(Request $request, Response $response)
	{
		$wantlist = $this->generate([
			"price_min" => 0.01,
			"price_max" => 3,
			"colors" => ["Blue", "Black"],
			"usage_min" => 2,
			"usage_max" => 9999999]);
		$this->renderer->render($response, 'staples.twig', array("wantlist" => $wantlist));
		return $response;
	}

	private function load($file)
	{
		$dataset = [];
		$file_handler = fopen($file, "r");
		while (($data = fgetcsv($file_handler, 1024, ";")) !== FALSE)
		{
			$dataset[] = [
			"name" => $data[0],
			"colors" => explode("-", $data[1]),
			"usage" => $data[2],
			"occurence" => $data[3],
			"price_low" => $data[4],
			"price_average" => $data[5],
			"price_high" => $data[6]
			];
		}
		fclose($file_handler);
		return ($dataset);
	}

	// Need to move this on an helper.
	private function hasColor($card, $colors)
	{
		$match = 0;
		foreach ($colors as $color)
		{
			if (in_array($color, $card['colors']))
				$match++;
		}
		if ($match > 0 && $match >= count($card['colors']))
			return (TRUE);
		return (FALSE);
	}

	private function generate($settings)
	{
		$wantlist = "";
		foreach ($this->dataset as $card)
		{
			if ($card['price_low'] >= $settings['price_min'] &&
				$card['price_low'] <= $settings['price_max'] &&
					$card['usage'] >= $settings['usage_min'] &&
						$card['usage'] <= $settings['usage_max'] &&
						$card['price_low'] != 0.0 &&
							$this->hasColor($card, $settings['colors']))
			{
				if (strpos($wantlist, $card['name']) == FALSE)
					$wantlist .= $card['occurence'] . "x " . $card['name'] . "\r\n";
			}
		}
		return ($wantlist);
	}
}
