<?php

namespace MTGScrapper\Utils;

class Helper
{
	public static function isBasicLand(string $name) : bool
	{
		if ($name !== "Plains" &&
				$name !== "Mountain" &&
					$name !== "Forest" &&
						$name !== "Swamp" &&
							$name !== "Island")
			return false;
		return true;
	}

	public static function colorsToString(Array $colors)
	{
		$string = '';
		foreach ($colors as $color)
		{
			$string .= $color . '-';
		}
		$string = substr($string, 0, -1);
		if (empty($string))
			$string = 'colorless';
		return ($string);
	}
}
