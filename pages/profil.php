<?php
// [DITAMBAHKAN] Memuat autoload dan service untuk mengambil data terbaru dari JSON berdasarkan ID pengguna
require_once __DIR__ . '/../src/autoload.php';

$memberService      = new \App\Services\MemberService();
$transactionService = new \App\Services\TransactionService();

$sessionUser = $_SESSION['user'] ?? [];
$userId      = (string) ($sessionUser['id'] ?? $sessionUser['userId'] ?? '');

// [DITAMBAHKAN] Mengambil data pengguna langsung dari users.json agar selalu sinkron
$dbUser = $userId !== '' ? $memberService->getById($userId) : null;
$user   = $dbUser ?? $sessionUser;

if ($dbUser !== null) {
    $_SESSION['user']['name']  = $dbUser['name'];
    $_SESSION['user']['email'] = $dbUser['email'];
    $_SESSION['user']['role']  = $dbUser['role'];
}

$name  = $user['name'] ?? '-';
$email = $user['email'] ?? '-';
$role  = $user['role'] ?? '-';

// [DITAMBAHKAN] Mengambil statistik peminjaman khusus milik pengguna dari transactions.json
$userTransactions = $userId !== '' ? $transactionService->getByUserId($userId) : [];
$totalUserLoans   = count($userTransactions);
$activeUserLoans  = count(array_filter(
    $userTransactions,
    static fn(array $t): bool => ($t['status'] ?? '') === 'borrowed' && empty($t['returnedAt'])
));

$initial = $name !== '-' 
    ? strtoupper(substr($name, 0, 1)) 
    : '?';

$roleLabel = match (strtolower((string) $role)) {
    'admin' => 'Administrator',
    'member' => 'Member',
    'user' => 'Pengguna',
    default => $role
};
?>

<section class="page-header">
    <div>
        <h1>Profil Saya</h1>
        <p>Informasi akun pengguna E-Book Library.</p>
    </div>
</section>

<section class="profile-container">

    <div class="profile-card">
        <div class="profile-avatar">
            <?= htmlspecialchars($initial) ?>
        </div>

        <h2><?= htmlspecialchars($name) ?></h2>

        <span class="profile-role">
            <?= htmlspecialchars($roleLabel) ?>
        </span>

        <p class="profile-description">
            Informasi akun yang sedang digunakan.
        </p>
    </div>

    <div class="content-card profile-information">
        <div class="content-card-header">
            <div>
                <h2>Informasi Pengguna</h2>
                <p>Detail informasi akun kamu.</p>
            </div>
        </div>

        <div class="profile-details">

            <div class="profile-detail-item">
                <span>Nama Lengkap</span>
                <strong><?= htmlspecialchars($name) ?></strong>
            </div>

            <div class="profile-detail-item">
                <span>Email</span>
                <strong><?= htmlspecialchars($email) ?></strong>
            </div>

            <div class="profile-detail-item">
                <span>ID Pengguna</span>
                <strong><?= htmlspecialchars((string) ($userId !== '' ? $userId : '-')) ?></strong>
            </div>

            <div class="profile-detail-item">
                <span>Jenis Akun</span>
                <strong><?= htmlspecialchars((string) $roleLabel) ?></strong>
            </div>

            <!-- [DITAMBAHKAN] Menampilkan ringkasan aktivitas berdasarkan data pengguna -->
            <div class="profile-detail-item">
                <span>Total Peminjaman</span>
                <strong><?= number_format($totalUserLoans) ?> Buku</strong>
            </div>

            <div class="profile-detail-item">
                <span>Sedang Dipinjam</span>
                <strong><?= number_format($activeUserLoans) ?> Buku</strong>
            </div>

        </div>

        <div class="profile-actions">
            <a href="index.php?page=<?= strtolower((string) $role) === 'admin' ? 'admin-dashboard' : 'dashboard' ?>" class="btn-primary">
                Kembali ke Dashboard
            </a>
        </div>
    </div>

</section>