<?php

namespace App\Models;

class Deck implements \JsonSerializable
{
	use JsonSerializeTrait;

	private		$id;
	private		$name;
	private		$author;
	private		$date;
	private		$id_event;
	private		$id_archetype;
	private		$cards;

	public function setId(int $id = 0): self
	{
		$this->id = $id;
		return $this;
	}

	public function getId(): ?int
	{
		return $this->id;
	}

	public function setName(string $name): self
	{
		$this->name = $name;
		return $this;	
	}

	public function getName(): ?string
	{
		return $this->name;
	}

	public function setAuthor(string $author): self
	{
		$this->author = $author;
		return $this;
	}

	public function getAuthor(): ?int
	{
		return $this->author;
	}

	public function setIdEvent(int $id_event): self
	{
		$this->id_event = $id_event;
		return $this;
	}

	public function getIdEvent(): ?int
	{
		return $this->id_event;
	}

	public function setIdArchetype(int $id_archetype): self
	{
		$this->id_archetype = $id_archetype;
		return $this;
	}

	public function getIdArchetype(): ?int
	{
		return $this->id_archetype;
	}

	public function setCards(array $cards): self
	{
		$this->cards = $cards;
		return $this;
	}

	public function getCards(): ?array
	{
		return $this->cards;
	}

	public function setDate(\DateTime $date): self
	{
		$this->date = $date;
		return $this;
	}

	public function getDate(): ?int
	{
		return $this->date;
	}
}
