<?php

namespace App\Models;

use JsonSerializable;

abstract class Media implements JsonSerializable
{
    protected string $id;
    protected string $title;

    public function __construct(string $id, string $title)
    {
        $this->id = $id;
        $this->title = $title;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    abstract public function isAvailable(): bool;
    abstract public function decrementAvailableCopies(): void;
    abstract public function incrementAvailableCopies(): void;
    abstract public function toArray(): array;

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
