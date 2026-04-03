<?php

namespace App\Domain;

class User {

    private ?int $id;
    private string $full_name;
    private string $email;
    private string $password;
    private string $created_at;
    private ?string $birth_date;
    private ?string $phone;
    private string $status;

    public function __construct(
        ?int $id,
        string $full_name,
        string $email,
        string $password,
        string $created_at,
        ?string $birth_date,
        ?string $phone,
        string $status,
    ) {
        $this->id = $id;
        $this->full_name = $full_name;
        $this->email = $email;
        $this->password = $password;
        $this->created_at = $created_at;
        $this->birth_date = $birth_date;
        $this->phone = $phone;
        $this->status = $status;
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function fullName(): string
    {
        return $this->full_name;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function password(): string
    {
        return $this->password;
    }

    public function createdAt(): string
    {
        return $this->created_at;
    }

    public function birthDate(): ?string
    {
        return $this->birth_date;
    }

    public function date(): string
    {
        return $this->createdAt();
    }

    public function phone(): ?string
    {
        return $this->phone;
    }

    public function status(): string
    {
        return $this->status;
    }

    public function setFullName(string $full_name): void
    {
        $this->full_name = $full_name;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }
}
