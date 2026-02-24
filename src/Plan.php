<?php

namespace App\Database;

class Plan 
{
    private ?int $id;
    private string $name;
    private float $price;
    private string $description;
    private int $active;
    private int $durantion_days;

    public function __construct(?int $id, string $name, float $price, string $description, int $active, int $durantion_days)
    {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->description = $description;
        $this->active = $active;
        $this->durantion_days = $durantion_days;
    }

    public function getId (){
        return $this->id;
    }

    public function getName(){
        return $this->name;
    }

    public function setName ($name){
        $this->name = $name;
    }

    public function getPrice(){
        return $this->price;
    }

    public function setPrice ($price){
        $this->price = $price;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function setDescription($description): void {
        $this->description = $description;
    }

    public function getActive(): int {
        return $this->active;
    }

    public function setActive($active): void {
        $this->active = $active;
    }

    public function getDays() {
        return $this->durantion_days;
    }

}