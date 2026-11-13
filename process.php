<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/src/autoload.php';

use App\Services\AuthService;
use App\Services\EbookService;
use App\Services\MemberService;
use App\Services\TransactionService;

$authService        = new AuthService();
$ebookService       = new EbookService();
$memberService      = new MemberService();
$transactionService = new TransactionService();

$action = $_POST['action'] ?? $_GET['action'] ?? '';

function redirectWithFlash(string $url, bool $success, string $message): void
{
    $_SESSION[$success ? 'flash_success' : 'flash_error'] = $message;
    header('Location: ' . $url);
    exit;
}

function requireAdminAccess(AuthService $authService): void
{
    if (!$authService->isLoggedIn() || !$authService->isAdmin()) {
        redirectWithFlash('index.php?page=login', false, 'Akses ditolak. Silakan login sebagai Admin.');
    }
}

function requireLoginAccess(AuthService $authService): array
{
    $user = $authService->currentUser();
    if (!$user) {
        redirectWithFlash('index.php?page=login', false, 'Silakan login terlebih dahulu.');
    }
    return $user;
}

switch ($action) {
    // --- AUTHENTICATION ---
    case 'login':
        $res = $authService->login($_POST['email'] ?? '', $_POST['password'] ?? '');
        if ($res['success']) {
            redirectWithFlash($res['redirect'], true, $res['message']);
        }
        redirectWithFlash('index.php?page=login', false, $res['message']);
        break;

    case 'register':
        $res = $authService->register(
            $_POST['name'] ?? '',
            $_POST['email'] ?? '',
            $_POST['password'] ?? '',
            $_POST['confirm_password'] ?? ''
        );
        $target = $res['success'] ? 'index.php?page=login' : 'index.php?page=register';
        redirectWithFlash($target, $res['success'], $res['message']);
        break;

    case 'logout':
        $authService->logout();
        session_start();
        redirectWithFlash('index.php?page=login', true, 'Anda telah berhasil logout.');
        break;

    // --- MANAJEMEN E-BOOK (ADMIN) ---
    case 'create_ebook':
        requireAdminAccess($authService);
        $res = $ebookService->create($_POST);
        $target = $res['success'] ? 'index.php?page=kelola-ebook' : 'index.php?page=tambah-ebook';
        redirectWithFlash($target, $res['success'], $res['message']);
        break;

    case 'update_ebook':
        requireAdminAccess($authService);
        $id  = $_POST['id'] ?? '';
        $res = $ebookService->update($id, $_POST);
        redirectWithFlash('index.php?page=kelola-ebook', $res['success'], $res['message']);
        break;

    case 'delete_ebook':
        requireAdminAccess($authService);
        $id  = $_POST['id'] ?? $_GET['id'] ?? '';
        $res = $ebookService->delete($id);
        redirectWithFlash('index.php?page=kelola-ebook', $res['success'], $res['message']);
        break;

    // --- MANAJEMEN MEMBER (ADMIN) ---
    case 'create_member':
        requireAdminAccess($authService);
        $res = $memberService->createMember(
            $_POST,
            (string) ($_POST['confirm_password'] ?? '')
        );
        $target = $res['success'] ? 'index.php?page=kelola-member' : 'index.php?page=tambah-member';
        redirectWithFlash($target, $res['success'], $res['message']);
        break;

    case 'update_member':
        requireAdminAccess($authService);
        $id  = $_POST['id'] ?? '';
        $res = $memberService->updateMember(
            $id,
            $_POST,
            (string) ($_POST['confirm_password'] ?? '')
        );
        // [DITAMBAHKAN] Sinkronisasi data session jika akun yang diedit sedang aktif login
        if ($res['success'] && ($_SESSION['user']['id'] ?? '') === $id) {
            $updatedUser = $memberService->getById($id);
            if ($updatedUser !== null) {
                $_SESSION['user']['name']  = $updatedUser['name'];
                $_SESSION['user']['email'] = $updatedUser['email'];
                $_SESSION['user']['role']  = $updatedUser['role'];
            }
        }
        // [DIUBAH] Jika gagal validasi, kembalikan ke form edit-member sesuai ID
        $target = $res['success'] ? 'index.php?page=kelola-member' : 'index.php?page=edit-member&id=' . urlencode($id);
        redirectWithFlash($target, $res['success'], $res['message']);
        break;

    // [DITAMBAHKAN] Pembaruan data profil oleh pengguna yang sedang login
    case 'update_profile':
        $currentUser = requireLoginAccess($authService);
        $id = (string) $currentUser['id'];
        $payload = [
            'name'     => $_POST['name'] ?? '',
            'email'    => $_POST['email'] ?? '',
            'password' => $_POST['password'] ?? '',
            'role'     => $currentUser['role'],
        ];
        $res = $memberService->updateMember(
            $id,
            $payload,
            (string) ($_POST['confirm_password'] ?? '')
        );
        if ($res['success']) {
            $updatedUser = $memberService->getById($id);
            if ($updatedUser !== null) {
                $_SESSION['user']['name']  = $updatedUser['name'];
                $_SESSION['user']['email'] = $updatedUser['email'];
            }
        }
        redirectWithFlash('index.php?page=profil', $res['success'], $res['message']);
        break;

    case 'delete_member':
        requireAdminAccess($authService);
        $id  = $_POST['id'] ?? $_GET['id'] ?? '';
        $res = $memberService->deleteMember($id);
        redirectWithFlash('index.php?page=kelola-member', $res['success'], $res['message']);
        break;

    // --- PEMINJAMAN & PENGEMBALIAN (MEMBER) ---
    case 'borrow_ebook':
        $user    = requireLoginAccess($authService);
        $ebookId = $_POST['ebookId'] ?? $_GET['ebookId'] ?? '';
        $days    = (int) ($_POST['duration'] ?? 7);
        $res     = $transactionService->borrowEbook($user['id'], $ebookId, $days > 0 ? $days : 7);
        $target  = $res['success'] ? 'index.php?page=peminjaman' : 'index.php?page=detail-ebook&id=' . urlencode($ebookId);
        redirectWithFlash($target, $res['success'], $res['message']);
        break;

    case 'return_ebook':
        $user  = requireLoginAccess($authService);
        $trxId = $_POST['transactionId'] ?? $_GET['transactionId'] ?? '';
        $ownerFilter = ($user['role'] === 'admin') ? null : $user['id'];
        $res   = $transactionService->returnEbook($trxId, $ownerFilter);
        $target = ($user['role'] === 'admin') ? 'index.php?page=admin-dashboard' : 'index.php?page=riwayat';
        redirectWithFlash($target, $res['success'], $res['message']);
        break;

    default:
        redirectWithFlash('index.php', false, 'Aksi tidak dikenali.');
        break;
}