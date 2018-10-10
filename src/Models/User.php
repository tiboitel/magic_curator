<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model implements \JsonSerializable
{
	use 		\App\Models\JsonSerializeTrait;

	private		$id;
	private		$token;
	private		$token_date_expired;
	private		$login;
	private		$password;
	protected	$table = 'Users';

	public function setId(int $id = 0) : self
	{
		$this->id = $id;
		return $this;
	}
	
	public function getId(): ?int
	{
		return $this->id;
	}

	public function getTokenDateExpired(): ?\DateTime
	{
		return $this->token_date_expired;
	}

	public function setTokenDateExpired(\DateTime $date) : self
	{
		$this->token_date_expired = $date;
		return $this;
	}

	public function getLogin(): ?string
	{
		return $this->login;
	}

	public function setLogin(string $login) : self
	{
		$this->login = $login;
		return $this;
	}

	public function getPassword(): ?string
	{
		return $this->password;
	}

	public function setPassword(string $password) : self
	{
		$this->password = $password;
		return $this;
	}

	public function getToken(): ?string
	{
		return $this->token;
	}

	public function	setToken($token) : self
	{
		$this->token = $token;
		return $this;
	}
	
	public function setLogged($logged) : ?bool
	{
		$_SESSION['is_authenticated'] = $logged;
		return $this;
	}
}
