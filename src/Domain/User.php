<?php

namespace App\Domain;

class User {

    private ?int $id;
    private string $full_name;
    private string $email;
    private string $password;
    private string $date;
    private string $phone;
    private string $status;

    public function __construct(
        ?int $id,
        string $full_name,
        string $email,
        string $password,
        string $date,
        string $phone,
        string $status
    ) {
        $this->id = $id;
        $this->full_name = $full_name;
        $this->email = $email;
        $this->password = $password;
        $this->date = $date;
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

    public function date(): string
    {
        return $this->date;
    }

    public function phone(): string
    {
        return $this->phone;
    }

    public function status(): string
    {
        return $this->status;
    }

}