<?php

namespace App\Domain;

class Professional 
{
	private ?int $id;
	private string $name;
	private string $specialty;
	private string $phone;
	private string $email;
	private bool $active;

	public function __construct(?int $id, string $name, string $specialty, string $phone, string $email, bool $active = true)
	{
		$this->id = $id;
		$this->name = $name;
		$this->specialty = $specialty;
		$this->phone = $phone;
		$this->email = $email;
		$this->active = $active;
	}

	public function getId(): ?int
	{
		return $this->id;
	}

	public function setId(?int $id): void
	{
		$this->id = $id;
	}

	public function getFullName(): string
	{
		return $this->name;
	}

	public function setFullName(string $name): void
	{
		$this->name = $name;
	}

	public function getSpecialty(): string
	{
		return $this->specialty;
	}

	public function setSpecialty(string $specialty): void
	{
		$this->specialty = $specialty;
	}

	public function getPhone(): string
	{
		return $this->phone;
	}

	public function setPhone(string $phone): void
	{
		$this->phone = $phone;
	}

	public function getEmail(): string
	{
		return $this->email;
	}

	public function setEmail(string $email): void
	{
		$this->email = $email;
	}

	public function isActive(): bool
	{
		return $this->active;
	}

	public function setActive(bool $active): void
	{
		$this->active = $active;
	}

}