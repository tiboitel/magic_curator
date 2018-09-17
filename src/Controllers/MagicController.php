<?php

namespace App\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;
use mtgsdk\Card;
use pdeans\Http\Client;

class MagicController extends AbstractController
{
	function show(Request $request, Response $response)
	{	
		$cards = Card::where(["set" => "DOM"])->all();
		file_put_contents("../database/dominaria.json", json_encode($cards, JSON_FORCE_OBJECT));
		foreach($cards as $card)
		{
			if (($usage = $this->usageInStandard($card->name)) > 0)
				file_put_contents("../database/curator_list.txt", "4x " . $card->name . " - " . $usage . "\r\n", FILE_APPEND);
		}
		return $response;
	}

	function usageInStandard($cardname)
	{
		$client = new Client([CURLOPT_SSL_VERIFYPEER => 0,
			CURLOPT_SSL_VERIFYHOST => 0
		]);
		$headers = [];
		$body = http_build_query([
			"current_page" => "",
			"event_titre" => "",
			"deck_titre" => "",
			"player" => "",
			"format" => "ST",
			"archetype_sel[VI]" => "",
			"archetype_sel[LE]" => "",
			"archetype_sel[MO]" => "",
			"archetype_sel[EX]" => "",
			"archetype_sel[ST]" => "",
			"archetype_sel[BL]" => "",
			"archetype_sel[PAU]" => "",
			"archetype_sel[EDH]" => "",
			"archetype_sel[HIGH]" => "",
			"archetype_sel[EDHP]" => "",
			"archetype_sel[CHL]" => "",
			"archetype_sel[PEA]" => "",
			"archetype_sel[EDHM]" => "",
			"MD_check" => 1,
			"SD_check" => 1,
			"cards" => $cardname
		]);
		$http_response = $client->post("https://www.mtgtop8.com/search", $headers, $body);
		preg_match("/([1-9]*) decks matching/m", $http_response->getBody(), $matches);
		return ($matches[1]);
	}
}
