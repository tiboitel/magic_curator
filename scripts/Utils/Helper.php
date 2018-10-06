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
			return true;
		return false;
	}
}
