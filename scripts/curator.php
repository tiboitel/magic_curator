#!/usr/bin/php
<?php
require __DIR__ . '/../vendor/autoload.php';

use mtgsdk\Card;
use pdeans\Http\Client;

class MTGScrapper
{
	private $client;

	public function __construct()
	{
		 $this->client = new Client([
			CURLOPT_SSL_VERIFYPEER => 0,
			CURLOPT_SSL_VERIFYHOST => 0
			]);
	}

	public function update_all_cards()
	{	
		$cards = $this->get_standard_cards();
		$cards_csv = "";
		foreach($cards as $card)
		{
			$usage = $this->usage_in_standard($card->name);
			printf("Current card: %s. Usage: %d. \r\n", $card->name, $usage);
			$price = $this->get_card_prices($card->name);
			print_r($price);
			$cards_csv .=  $card->name . ";" . $usage . ";" .  $price['low'] . ";" . $price['average'] . ";" . $price['high'] .  "\r\n";
			// create an auction object with card name id usage price 
		}
		file_put_contents("../database/database.csv", $cards_csv);
	}	



	public function get_standard_cards()
	{
		// $dom = 	$cards = Card::where(["set" => "DOM"])->where(["name" => "Knight of Malice"])->all();
			$dom = 	$cards = Card::where(["set" => "DOM"])->all();
			$m19 = 	$cards = Card::where(["set" => "M19"])->all();
			$rlx = 	$cards = Card::where(["set" => "RIX"])->all();
			$xln = 	$cards = Card::where(["set" => "XLN"])->all();
			$standard = array_merge($dom, $m19, $rlx, $xln);
		return ($standard);
	}

	public function get_card_prices($card)
	{
		$headers = [];
		$http_response = $this->client->get("http://partner.tcgplayer.com/x3/phl.asmx/p?pk=TCGTEST&s=&p=" . urlencode($card));
		preg_match_all("/>(\d+.\d+)</m", $http_response->getBody(), $matches);
		$price = ["high" => $matches[1][0], "low" => $matches[1][1], "average" => $matches[1][2]];
		return ($price);
	}

	public function usage_in_standard($cardname)
	{
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
				"SB_check" => 1,
				"cards" => $cardname,
				"date_start" => "01/05/2018",
				"date_end" => ""
					]);
		$http_response = $this->client->post("https://www.mtgtop8.com/search", $headers, $body);
		preg_match("/([0-9]*) decks matching/m", $http_response->getBody(), $matches);
		return ($matches[1]);
	}
}

$scrapper = new MTGScrapper();
$scrapper->update_all_cards();
//$scrapper->update_all_cards();
?>
