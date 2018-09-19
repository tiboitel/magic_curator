<?php

namespace App\Validation;

use Psr\Http\Message\ServerRequestInterface;

class Validator
{
	protected $errors = [];
	
	public function validate(ServerRequestInterface $request, array $rules)
	{
	}

	public function validateArray()
	{
	}

	public function failed()
	{
		return !empty($this->errors);
	}

	private function getValue($values, $field)
	{
		return isset($values[$field]) ? $values[$field] : null;
	}


}
