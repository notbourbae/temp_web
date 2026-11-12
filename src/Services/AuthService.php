<?php

namespace App\Services;

use App\Models\User;

class AuthService
{
    private string $usersFile;

    public function __construct(?string $usersFile = null)
    {
        $this->usersFile = $usersFile ?? __DIR__ . '/../../data/users.json';
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function register(string $name, string $email, string $password, string $confirmPassword = ''): array
    {
        $name  = trim($name);
        $email = strtolower(trim($email));

        if ($name === '' || $email === '' || $password === '') {
            return ['success' => false, 'message' => 'Nama, email, dan password wajib diisi.'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Format email tidak valid.'];
        }

        if (strlen($password) < 6) {
            return ['success' => false, 'message' => 'Password minimal 6 karakter.'];
        }

        if ($confirmPassword !== '' && $password !== $confirmPassword) {
            return ['success' => false, 'message' => 'Konfirmasi password tidak cocok.'];
        }

        $users = JsonStorage::read($this->usersFile);
        foreach ($users as $existing) {
            if (strtolower($existing['email'] ?? '') === $email) {
                return ['success' => false, 'message' => 'Email sudah terdaftar.'];
            }
        }

        $newUser = new User(
            JsonStorage::generateId('USR', $users),
            $name,
            $email,
            password_hash($password, PASSWORD_DEFAULT),
            'member'
        );

        $users[] = $newUser->toArray();
        JsonStorage::write($this->usersFile, $users);

        return ['success' => true, 'message' => 'Registrasi berhasil. Silakan login.', 'user' => $newUser->toArray()];
    }

    public function login(string $email, string $password): array
    {
        $email = strtolower(trim($email));

        if ($email === '' || $password === '') {
            return ['success' => false, 'message' => 'Email dan password wajib diisi.'];
        }

        $users = JsonStorage::read($this->usersFile);
        foreach ($users as $user) {
            if (strtolower($user['email'] ?? '') === $email) {
                if (!password_verify($password, $user['password'] ?? '')) {
                    return ['success' => false, 'message' => 'Email atau password salah.'];
                }

                session_regenerate_id(true);
                $_SESSION['user'] = [
                    'id'    => $user['id'],
                    'name'  => $user['name'],
                    'email' => $user['email'],
                    'role'  => $user['role'],
                ];

                $redirect = ($user['role'] === 'admin')
                    ? 'index.php?page=admin/dashboard'
                    : 'index.php?page=dashboard';

                return [
                    'success'  => true,
                    'message'  => 'Login berhasil.',
                    'role'     => $user['role'],
                    'redirect' => $redirect,
                ];
            }
        }

        return ['success' => false, 'message' => 'Email atau password salah.'];
    }

    public function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
    }

    public function currentUser(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public function isLoggedIn(): bool
    {
        return isset($_SESSION['user']['id']);
    }

    public function isAdmin(): bool
    {
        return ($_SESSION['user']['role'] ?? '') === 'admin';
    }
}
