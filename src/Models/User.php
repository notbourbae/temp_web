<?php

namespace App\Models;

class User
{
    public string $id;
    public string $name;
    public string $email;
    public string $password;
    public string $role;

    public function __construct(
        string $id,
        string $name,
        string $email,
        string $password,
        string $role = 'member'
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            (string) ($data['id'] ?? ''),
            (string) ($data['name'] ?? ''),
            (string) ($data['email'] ?? ''),
            (string) ($data['password'] ?? ''),
            (string) ($data['role'] ?? 'member')
        );
    }

    public function toArray(): array
    {
        return [
            'id'       => $this->id,
            'name'     => $this->name,
            'email'    => $this->email,
            'password' => $this->password,
            'role'     => $this->role,
        ];
    }
}
