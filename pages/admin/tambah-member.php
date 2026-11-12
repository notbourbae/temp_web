
<?php
require_once __DIR__ . '/../../src/autoload.php';

$memberService = new \App\Services\MemberService();
$memberId = trim($_GET['id'] ?? '');
$member = $memberId === '' ? null : $memberService->getById($memberId);
$isEditing = $memberId !== '';

function memberFormValue(string $key, ?array $member): string
{
    return htmlspecialchars((string) ($member[$key] ?? ''), ENT_QUOTES, 'UTF-8');
}
?>

<?php if ($isEditing && (!$member || ($member['role'] ?? '') !== 'member')): ?>
    <section class="content-card">
        <h1>Member tidak ditemukan</h1>
        <a href="index.php?page=kelola-member" class="btn-primary">Kembali ke daftar</a>
    </section>
    <?php return; ?>
<?php endif; ?>

<section class="page-header">
    <div>
        <h1><?= $isEditing ? 'Edit Member' : 'Tambah Member' ?></h1>
        <p><?= $isEditing ? 'Perbarui informasi member.' : 'Tambahkan anggota baru ke dalam perpustakaan.' ?></p>
    </div>
</section>

<section class="content-card member-form-card">

    <div class="content-card-header">
        <div>
            <h2><?= $isEditing ? 'Perbarui Informasi Member' : 'Informasi Member' ?></h2>
            <p>Lengkapi informasi anggota di bawah ini.</p>
        </div>
    </div>

    <form action="process.php" method="POST" class="member-form">
        <input type="hidden" name="action" value="<?= $isEditing ? 'update_member' : 'create_member' ?>">
        <?php if ($isEditing): ?>
            <input type="hidden" name="id" value="<?= htmlspecialchars($memberId, ENT_QUOTES, 'UTF-8') ?>">
        <?php endif; ?>

        <div class="member-form-grid">

            <div class="form-group">
                <label for="memberName">Nama Lengkap</label>
                <input
                    type="text"
                    id="memberName"
                    name="name"
                    placeholder="Masukkan nama lengkap"
                    value="<?= memberFormValue('name', $member) ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="memberEmail">Email</label>
                <input
                    type="email"
                    id="memberEmail"
                    name="email"
                    placeholder="contoh@email.com"
                    value="<?= memberFormValue('email', $member) ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="memberPassword"><?= $isEditing ? 'Password baru (opsional)' : 'Password' ?></label>
                <input
                    type="password"
                    id="memberPassword"
                    name="password"
                    placeholder="<?= $isEditing ? 'Kosongkan jika tidak ingin mengganti' : 'Masukkan password' ?>"
                    minlength="6"
                    <?= $isEditing ? '' : 'required' ?>
                >
            </div>

            <div class="form-group">
                <label for="confirmPassword">Konfirmasi <?= $isEditing ? 'Password Baru' : 'Password' ?></label>
                <input
                    type="password"
                    id="confirmPassword"
                    name="confirm_password"
                    placeholder="Ulangi password"
                    minlength="6"
                    <?= $isEditing ? '' : 'required' ?>
                >
            </div>

        </div>

        <div class="member-form-actions">
            <a href="index.php?page=kelola-member" class="btn-reset">
                Kembali
            </a>

            <button type="submit" class="btn-primary">
                <?= $isEditing ? 'Simpan Perubahan' : 'Simpan Member' ?>
            </button>
        </div>

    </form>

</section>
