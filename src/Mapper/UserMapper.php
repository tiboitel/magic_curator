<?php

namespace App\Mapper;

use App\Models;

class UserMapper
{
	private	$db;
	protected $table = [];

	public function __construct($db)
	{
		$this->db = $db;
		$file = fopen($db, "r+");
		while (($data = fgetcsv($file, 1024, ";")) !== FALSE)
		{
			$this->table[] = [
				"login" => $data[0],
				"password" => $data[1]
				];
		}
		fclose($file);
	}

	public function addUser($login, $password)
	{
		if (!in_array($login, $this->table))
		{
			$content_string = "";
			$content_string = $login . ";" . password_hash($password, PASSWORD_BCRYPT) . "\r\n";
			file_put_contents($this->db, $content_string);
		}
	}

	public function findByLogin(string $login) : ?User
	{
		foreach ($table as $user)
		{
			if ($user['login'] === $login)
				return $user;
		} 
		return null;
	}
}
