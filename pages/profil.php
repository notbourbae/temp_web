
<?php
$user = $_SESSION['user'] ?? [];

$name = $user['name'] ?? '-';
$email = $user['email'] ?? '-';
$userId = $user['id'] ?? $user['userId'] ?? '-';
$role = $user['role'] ?? '-';

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
                <strong><?= htmlspecialchars((string) $userId) ?></strong>
            </div>

            <div class="profile-detail-item">
                <span>Jenis Akun</span>
                <strong><?= htmlspecialchars((string) $roleLabel) ?></strong>
            </div>

        </div>

        <div class="profile-actions">
            <a href="index.php?page=dashboard" class="btn-primary">
                Kembali ke Dashboard
            </a>
        </div>
    </div>

</section>
