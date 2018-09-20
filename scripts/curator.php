#!/usr/bin/php
<?php
require __DIR__ . '/../vendor/autoload.php';

use mtgsdk\Card;
use pdeans\Http\Client;
use Sunra\PhpSimple\HtmlDomParser;

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
			$usage = $this->get_usage_in_standard($card->name);
			printf("Current card: %s. Usage: %d. \r\n", $card->name, $usage);
			$price = $this->get_card_prices($card->name);
			print_r($price);
			$cards_csv .=  $card->name . ";" . $usage . ";" .  $price['low'] . ";" . $price['average'] . ";" . $price['high'] .  "\r\n";
			// create an auction object with card name id usage price 
		}
		file_put_contents("../database/database.csv", $cards_csv);
	}	


	public function	get_decks_list($format, $max_page = 50)
	{
		$decks_list = [];
		$headers = [];
		$current_page = 0;
		$url = "https://mtgdecks.net/";
		for ($current_page = 0; $current_page < 50; $current_page++)
		{
		// Better use http_build_url if pecl is enabled.
		$query = $url . $format . "/decklists/" ;
		$args = http_build_query([
			"page" => $current_page
			], ":");
		$query .= $args;
		$http_response = $this->client->get($query);
		$dom = HtmlDomParser::str_get_html($http_response->getBody());
		$content = $dom->find("div[class=decks index]", 0);
		$decks_table = $content->children(0)->children(1)->children(0);
		foreach ($decks_table->find('tr') as $tr)
		{
			$column_id = 0;
			$deck = [];
			foreach ($tr->find('td') as $td)
			{
				switch ($column_id)
				{
					case 0:
						// Place in tournament.
					break;
					case 1:
						$node = $td->children(0)->children(0);
						$deck['name'] = $node->plaintext;
						$deck['source'] = $url . $node->href;
						$deck['id'] = substr($deck['source'], strrpos($deck['source'], '-') + 1);
					break;
					case 2:
						$deck['archetype'] = $td->plaintext;
					break;
					case 3:
						// Colors
					break;
					case 4:
						// Number of player
					break;
					case 5: 
						// Date
						
					break;
					case 6:
						// Price low.
					break;
				}
				$column_id++;
			}
			$decks_list[] = $deck;
		}
		}
		print_r($decks_list);
		return ($decks_list);
		//	$content = $http_response->getBody();
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

	public function get_usage_in_standard($cardname)
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
//$scrapper->update_all_cards();
file_put_contents("../database/decklist.json", json_encode($scrapper->get_decks_list("Standard")));
?>
