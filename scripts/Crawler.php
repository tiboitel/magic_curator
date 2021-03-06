#!/usr/bin/php
<?php
namespace MTGScrapper;

require __DIR__ . '/../vendor/autoload.php';

use mtgsdk\Card;
use pdeans\Http\Client;
use Sunra\PhpSimple\HtmlDomParser;
use App\Models;

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
			if (Utils\Helper::isBasicLand($card->name))
				{
				printf("Current card: %s. Usage: %d. \r\n", $card->name, $usage);
				$price = $this->get_card_prices($card->name);
				$cards_csv .=  $card->name . ";" . Utils\Helper::colorsToString(isset($card->colors) ? $card->colors : array()) . ";" . $usage . ";" . $this->get_occurence_per_deck($card->name) . ";" .  $price['low'] . ";" . $price['average'] . ";" . $price['high'] .  "\r\n";
			}
			// create an auction object with card name id usage price 
		}
		file_put_contents("../database/database.csv", $cards_csv);
	}	


	public function get_all_decks()
	{
		$decks = [];
		$decklists = json_decode(file_get_contents("../database/decklist.json"));
		foreach ($decklists as $decklist)
		{
			$deck = $this->get_deck($decklist->source);
			$decks[] = $deck;
		}
		return ($decks);
	}

	public function	update_decklist($format, $max_page)
	{
		$decks_list = [];
		$headers = [];
		$current_page = 0;
		$url = "https://mtgdecks.net/";
		for ($current_page = 1; $current_page <= $max_page; $current_page++)
		{
			// Better use http_build_url if pecl is enabled.
			$query = $url . $format . "/decklists/" ;
			$args = "page:" . $current_page;
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
							$deck['author'] = substr($td->children(3)->plaintext, 3);
							// need to use better function to clear html entitites
							$deck['author'] = str_replace("&nbsp;",  "", $deck['author']);
							break;
						case 2:
							$deck['archetype'] = $td->plaintext;
							break;
						case 3:
							$color = "";
							foreach ($td->find('span') as $span)
							{
								if ($span->class !== "small-icon")
								{
									$class = explode(" ", $span->class);
									$color .= substr($class[2], strrpos($class[2], '-') + 1);
								}	
							}
							$deck['color'] = strtoupper($color);
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
				if (isset($deck) && !empty($deck))
					$decks_list[] = $deck;
			}
		}
		return ($decks_list);
		//	$content = $http_response->getBody();
	}

	public function get_deck($url)
	{
	 	$deck = new \App\Models\Deck();
		$headers = [];
		$http_response = $this->client->get($url);
		$dom = HtmlDomParser::str_get_html($http_response->getBody());
		$deck_info = $dom->find("div[class=deckInfo col-sm-8]", 0);
		// Get deck id.
		$deck->setId((int)(substr($url, strrpos($url, "-") + 1)));
		// Get deck name.
		$deck->setName(substr($deck_info->children(0)->children(0)->plaintext, 0, -1));
		// Get deck author;
		$tmp = $deck_info->children(0)->children(1)->plaintext;
		$deck->setAuthor(substr($tmp, strpos($tmp, ":") + 2, -1));
		// Get deck date
		$date = (empty($deck_info->children(4)->plaintext)) ? ($deck_info->children(3)->plaintext) : ($deck_info->children(4)->plaintext);
		$deck->setDate(\DateTime::createFromFormat(" j-M-Y", $date));
		// Shit not done
		$deck->setIdEvent(0);
		$deck->setIdArchetype(0);
		// Get cards
		$deck_info = $dom->find("div[class=wholeDeck]", 0);
		$cards = [];
		foreach ($deck_info->find("table") as $table)
		{
			foreach ($table->find("tr[class=cardItem]") as $cardItem)
			{
				$line = $cardItem->children(0)->plaintext;
				$number = substr($line, 1, 1);
				$name = substr($line, strpos($line, ";") + 1, -13);
				$cards[] = array('number' => $number, 'name' => htmlspecialchars_decode($name, ENT_QUOTES));
			}
		}
		$deck->setCards($cards);
		return ($deck);
	}

	public function get_standard_cards()
	{
		// $standard  = Card::where(["set" => "DOM"])->where(["name" => "Knight of Malice"])->all();
		//$standard = Card::where(["gameFormat" => "Standard"])->all();
		$dom = Card::where(["set" => "DOM"])->all();
		$m19 = Card::where(["set" => "M19"])->all();
		$rlx = Card::where(["set" => "RIX"])->all();
		$xln = Card::where(["set" => "XLN"])->all();
		$grn = Card::where(["set" => "GRN"])->all();
		$standard = array_merge($dom, $m19, $rlx, $xln, $grn);
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
		$decks = json_decode(file_get_contents("../database/decks.json"));
		$usage = 0;
		foreach ($decks as $deck)
		{
			foreach ($deck->cards as $card)
			{
				if ($card->name === $cardname)
				{
					$usage++;
				}
			}
		}
		return ($usage);
	}

	public function get_occurence_per_deck($cardname)
	{
		$decks = json_decode(file_get_contents("../database/decks.json"));
		$usage = 0;
		$usage_in_deck = 0;
		foreach ($decks as $deck)
		{
			foreach ($deck->cards as $card)
			{
				if ($card->name === $cardname)
				{
					$usage_in_deck++;
					$usage += $card->number;
				}
			}
		}
		if ($usage_in_deck == 0)
			return (0);
		$usage = ceil($usage / $usage_in_deck);
		return ($usage);

	}
	
	public function get_usage_in_standard_on_mtg_top_8($cardname)
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
file_put_contents("../database/decklist.json", json_encode($scrapper->update_decklist("Standard", 9)));
file_put_contents("../database/decks.json", json_encode($scrapper->get_all_decks()));
?>
