<?php

namespace App\Services;

use App\Models\User;

class MemberService
{
    private string $usersFile;
    private string $transactionsFile;

    public function __construct(?string $usersFile = null, ?string $transactionsFile = null)
    {
        $this->usersFile        = $usersFile ?? __DIR__ . '/../../data/users.json';
        $this->transactionsFile = $transactionsFile ?? __DIR__ . '/../../data/transactions.json';
    }

    public function getAllMembers(?string $search = null): array
    {
        $users = JsonStorage::read($this->usersFile);
        $members = array_filter($users, fn($u) => ($u['role'] ?? '') === 'member');

        if ($search !== null && trim($search) !== '') {
            $kw = strtolower(trim($search));
            $members = array_filter($members, function ($m) use ($kw) {
                return str_contains(strtolower($m['name'] ?? ''), $kw)
                    || str_contains(strtolower($m['email'] ?? ''), $kw)
                    || str_contains(strtolower($m['id'] ?? ''), $kw);
            });
        }

        return array_values($members);
    }

    public function getById(string $id): ?array
    {
        foreach (JsonStorage::read($this->usersFile) as $user) {
            if (($user['id'] ?? '') === $id) {
                return $user;
            }
        }
        return null;
    }

    public function createMember(array $data, string $confirmPassword = ''): array
    {
        $name     = trim($data['name'] ?? '');
        $email    = strtolower(trim($data['email'] ?? ''));
        $password = (string) ($data['password'] ?? '');
        $role     = trim($data['role'] ?? 'member');

        if ($name === '' || $email === '' || $password === '') {
            return ['success' => false, 'message' => 'Nama, email, dan password member wajib diisi.'];
        }

        if (!in_array($role, ['member', 'admin'], true)) {
            return ['success' => false, 'message' => 'Role akun tidak valid.'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Format email tidak valid.'];
        }

        if (strlen($password) < 6) {
            return ['success' => false, 'message' => 'Password minimal 6 karakter.'];
        }

        if ($password !== $confirmPassword) {
            return ['success' => false, 'message' => 'Konfirmasi password tidak cocok.'];
        }

        $users = JsonStorage::read($this->usersFile);
        foreach ($users as $existing) {
            if (strtolower($existing['email'] ?? '') === $email) {
                return ['success' => false, 'message' => 'Email sudah digunakan oleh akun lain.'];
            }
        }

        $member = new User(
            JsonStorage::generateId('USR', $users),
            $name,
            $email,
            password_hash($password, PASSWORD_DEFAULT),
            $role
        );

        $users[] = $member->toArray();
        JsonStorage::write($this->usersFile, $users);

        $accountType = $role === 'admin' ? 'Administrator' : 'Member';
        return ['success' => true, 'message' => $accountType . ' baru berhasil ditambahkan.', 'data' => $member->toArray()];
    }

    public function updateMember(string $id, array $data, string $confirmPassword = ''): array
    {
        $name     = trim($data['name'] ?? '');
        $email    = strtolower(trim($data['email'] ?? ''));
        $password = (string) ($data['password'] ?? '');
        $role     = trim($data['role'] ?? 'member');

        if ($name === '' || $email === '') {
            return ['success' => false, 'message' => 'Nama dan email wajib diisi.'];
        }

        if (!in_array($role, ['member', 'admin'], true)) {
            return ['success' => false, 'message' => 'Role akun tidak valid.'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Format email tidak valid.'];
        }

        $users = JsonStorage::read($this->usersFile);
        foreach ($users as $existing) {
            if (($existing['id'] ?? '') !== $id && strtolower($existing['email'] ?? '') === $email) {
                return ['success' => false, 'message' => 'Email sudah digunakan oleh akun lain.'];
            }
        }

        $found = false;
        foreach ($users as &$user) {
            if (($user['id'] ?? '') === $id && ($user['role'] ?? '') === 'member') {
                $user['name']  = $name;
                $user['email'] = $email;
                $user['role']  = $role;
                if ($password !== '') {
                    if (strlen($password) < 6) {
                        return ['success' => false, 'message' => 'Password baru minimal 6 karakter.'];
                    }
                    if ($password !== $confirmPassword) {
                        return ['success' => false, 'message' => 'Konfirmasi password tidak cocok.'];
                    }
                    $user['password'] = password_hash($password, PASSWORD_DEFAULT);
                }
                $found = true;
                break;
            }
        }
        unset($user);

        if (!$found) {
            return ['success' => false, 'message' => 'Data member tidak ditemukan.'];
        }

        JsonStorage::write($this->usersFile, $users);
        return ['success' => true, 'message' => 'Data akun berhasil diperbarui.'];
    }

    public function deleteMember(string $id): array
    {
        $transactions = JsonStorage::read($this->transactionsFile);
        foreach ($transactions as $trx) {
            if (($trx['userId'] ?? '') === $id && ($trx['status'] ?? '') === 'borrowed') {
                return ['success' => false, 'message' => 'Member tidak dapat dihapus karena masih memiliki pinjaman aktif.'];
            }
        }

        $users = JsonStorage::read($this->usersFile);
        $filtered = array_filter($users, fn($u) => !(($u['id'] ?? '') === $id && ($u['role'] ?? '') === 'member'));

        if (count($filtered) === count($users)) {
            return ['success' => false, 'message' => 'Data member tidak ditemukan.'];
        }

        JsonStorage::write($this->usersFile, $filtered);
        return ['success' => true, 'message' => 'Member berhasil dihapus.'];
    }
}
