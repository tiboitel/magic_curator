<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Binder implements \JsonSerializable
{
	protected	$table = 'Binders';
	private		$id;
	private		$user_id;
	private		$cards;

	public function setId(int $id = 0) : self
	{
		$this->id = $id;
		return $this;
	}
	
	public function getId(): ?int
	{
		return $this->id;
	}

	public function getUserId(int $user_id = 0) : self
	{
		$this->user_id = $user_id;
		return $this;
	}
	
	public function setUserId(): ?int
	{
		return $this->user_id;
	}
}

