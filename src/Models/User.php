<?php

declare(strict_types=1);

namespace App\Models;

class User
{
    private string $id;
    private string $name;
    private string $email;
    private string $password;
    private string $role;
    private string $createdAt;

    public function __construct(
        string $id,
        string $name,
        string $email,
        string $password,
        string $role = 'member',
        ?string $createdAt = null
    ) {
        $this->setId($id);
        $this->setName($name);
        $this->setEmail($email);
        $this->setPassword($password);
        $this->setRole($role);
        $this->createdAt = $createdAt ?? date('Y-m-d H:i:s');
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $cleanId = trim($id);
        if ($cleanId === '') {
            throw new \InvalidArgumentException('ID pengguna wajib diisi.');
        }
        $this->id = $cleanId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $cleanName = trim($name);
        if (mb_strlen($cleanName) < 2) {
            throw new \InvalidArgumentException('Nama lengkap minimal 2 karakter.');
        }
        $this->name = $cleanName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $cleanEmail = trim($email);
        if (!filter_var($cleanEmail, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Format email tidak valid.');
        }
        $this->email = $cleanEmail;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        if (trim($password) === '') {
            throw new \InvalidArgumentException('Password tidak boleh kosong.');
        }

        // Jika belum berupa hash PHP (misal saat registrasi baru), lakukan hashing otomatis
        $info = password_get_info($password);
        $this->password = ($info['algo'] !== null)
            ? $password
            : password_hash($password, PASSWORD_DEFAULT);
    }

    public function verifyPassword(string $plainPassword): bool
    {
        return password_verify($plainPassword, $this->password);
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): void
    {
        $cleanRole = strtolower(trim($role));
        $allowed = ['admin', 'member'];
        if (!in_array($cleanRole, $allowed, true)) {
            throw new \InvalidArgumentException("Role harus 'admin' atau 'member'.");
        }
        $this->role = $cleanRole;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            (string) ($data['id'] ?? ''),
            (string) ($data['name'] ?? ''),
            (string) ($data['email'] ?? ''),
            (string) ($data['password'] ?? ''),
            (string) ($data['role'] ?? 'member'),
            isset($data['createdAt']) ? (string) $data['createdAt'] : null
        );
    }

    public function toArray(): array
    {
        return [
            'id'        => $this->id,
            'name'      => $this->name,
            'email'     => $this->email,
            'password'  => $this->password,
            'role'      => $this->role,
            'createdAt' => $this->createdAt,
        ];
    }
}