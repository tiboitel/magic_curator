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
				// printf("Current card: %s. Usage: %d. \r\n", $card->name, $usage);
				$price = $this->get_card_prices($card->name);
				$cards_csv .=  $card->name . ";" . Utils\Helper::colorsToString(isset($card->colors) ? $card->colors : array()) . ";" . $usage . ";" . $this->get_occurence_per_deck($card->name) . ";" .  $price['low'] . ";" . $price['average'] . ";" . $price['high'] .  "\r\n";
			}
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
						$node = $td->children(0);
						$deck['name'] = $node->plaintext;
						$deck['source'] = $url . $node->href;
						$deck['id'] = substr($deck['source'], strrpos($deck['source'], '-') + 1);							
						$deck['author'] = substr($td->children(2)->plaintext, 2);
						// need to use better function to clear html entitites
						$deck['author'] = str_replace("&nbsp;",  "", $deck['author']);
						break;
					case 2:
						$node = $td->children(0);
						$deck['archetype'] = trim($node->plaintext);
						$color = "";
						$node = $td->children(1);
						if (isset($node))
						{
							foreach ($node->find('span') as $span)
							{
								$class = explode(" ", $span->class);
								if (isset($class[2]))
									$color .= substr($class[2], strrpos($class[2], '-') + 1);
							}
							$deck['color'] = strtoupper($color);
						}
						break;
					}
					// Colors
					$column_id++;
				}
			}
			if (isset($deck) && !empty($deck))
				$decks_list[] = $deck;
		}
		return ($decks_list);
	}

	public function get_deck($url)
	{
		$deck = new \App\Models\Deck();
		$headers = [];
		$http_response = $this->client->get($url);
		$dom = HtmlDomParser::str_get_html($http_response->getBody());
		$deck_info = $dom->find("div[class=deckInfo col-sm-6]", 0);
		// Get deck id.
		$deck->setId((int)(substr($url, strrpos($url, "-") + 1)));
		// Get deck name.
		$data = explode("&mdash;", trim($deck_info->children(0)->plaintext));
		$deck->setName(substr($data[0], 0, strpos($data[0], ".")));
		// Get deck author;
		$deck->setAuthor(substr($data[0], strrpos($data[0], ":") + 2, -2));
		// Get deck date
		$date = $data[2];
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
				$line =str_replace("&nbsp;", "", $cardItem->children(0)->plaintext);
				$data = explode(" ", $line);
				$cards[] = array('number' => $data[0], 'name' => htmlspecialchars_decode($data[1], ENT_QUOTES));
			}
		}
		$deck->setCards($cards);
		return ($deck);
	}

	public function get_standard_cards()
	{
		$standard = Card::where(["gameFormat" => "Standard"])->all();
		/*$dom = Card::where(["set" => "ELD"])->all();
		$m19 = Card::where(["set" => "M21"])->all();
		$rlx = Card::where(["set" => "THB"])->all();
		$xln = Card::where(["set" => "IKO"])->all();
		$grn = Card::where(["set" => "ZNR"])->all();*/
		//$khm = Card::where(["set" => "KHM"])->all();
		// $standard = array_merge($dom, $m19, $rlx, $xln, $grn, $khm);
		return ($standard);
	}
	/**
	 * 
	 *
	 */
	public function get_card_prices($card)
	{
		$headers = [];
		$http_response = $this->client->get("https://partner.tcgplayer.com/x3/phl.asmx/p?pk=TCGTEST&s=&p=" . urlencode($card));
		preg_match_all("/>(\d+.\d+)</m", $http_response->getBody(), $matches);
		if (isset($matches[1][0]) && isset($matches[1][1]) && isset($matches[1][2]))
			$price = ["high" => $matches[1][0], "low" => $matches[1][1], "average" => $matches[1][2]];
		else
			$price = ["high" => 0.00, "low" => 0.00, "average" => 0.00];
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
					$usage++;
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

	/*
	 * Good working order.
	 */	
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
			"cards" => urlencode($cardname),
			"date_start" => "01/02/2021",
			"date_end" => ""
		]);
		$http_response = $this->client->post("https://www.mtgtop8.com/search", $headers, $body);
		preg_match("/([0-9]*) decks matching/m", $http_response->getBody(), $matches);
		return ($matches[1]);
	}
}

$scrapper = new MTGScrapper();
/* Update all cards infomations, then, update the whole decklist. */
$scrapper->update_all_cards();
//file_put_contents("../database/decklist.json", json_encode($scrapper->update_decklist("Standard", 30)));
//file_put_contents("../database/decks.json", json_encode($scrapper->get_all_decks()));
