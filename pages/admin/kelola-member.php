
<?php
require_once __DIR__ . '/../../src/autoload.php';

$search = trim($_GET['search'] ?? '');
$memberService = new \App\Services\MemberService();
$members = $memberService->getAllMembers($search);
$filteredMembers = $members;
$totalMembers = count($memberService->getAllMembers());
?>

<section class="page-header admin-member-header">
    <div>
        <h1>Kelola Member</h1>
        <p>Kelola informasi anggota perpustakaan.</p>
    </div>

    <a href="index.php?page=tambah-member" class="btn-primary">
        + Tambah Member
    </a>
</section>

<section class="content-card admin-member-content">

    <div class="content-card-header">
        <div>
            <h2>Daftar Member</h2>
            <p>
                Menampilkan <?= count($filteredMembers) ?>
                dari <?= $totalMembers ?> member.
            </p>
        </div>
    </div>

    <form method="GET" action="index.php" class="admin-member-toolbar">

        <input type="hidden" name="page" value="kelola-member">

        <div class="admin-member-search">
            <input
                type="text"
                name="search"
                placeholder="Cari nama atau email member..."
                value="<?= htmlspecialchars($search) ?>"
            >
        </div>

        <button type="submit" class="btn-primary">Cari</button>

        <a href="index.php?page=kelola-member" class="btn-reset">
            Reset
        </a>

    </form>

    <?php if (empty($filteredMembers)): ?>

        <div class="empty-state">
            <div class="empty-state-icon">👥</div>
            <h3><?= $search === '' ? 'Belum Ada Data Member' : 'Member Tidak Ditemukan' ?></h3>
            <p>
                <?= $search === '' ? 'Data anggota akan muncul setelah ditambahkan.' : 'Coba kata kunci pencarian lainnya.' ?>
            </p>
        </div>

    <?php else: ?>

        <div class="table-wrapper">
            <table class="admin-member-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nama Member</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($filteredMembers as $index => $member): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>

                            <td>
                                <strong>
                                    <?= htmlspecialchars($member['name'] ?? '-') ?>
                                </strong>
                            </td>

                            <td>
                                <?= htmlspecialchars($member['email'] ?? '-') ?>
                            </td>

                            <td>
                                <span class="member-role-badge">
                                    <?= htmlspecialchars($member['role'] ?? '-') ?>
                                </span>
                            </td>

                            <td>
                                <span class="status-badge history-returned">
                                    <?= htmlspecialchars($member['status'] ?? 'Aktif') ?>
                                </span>
                            </td>

                            <td>
                                <div class="admin-member-actions">
                                    <a class="btn-member-edit" href="index.php?page=edit-member&id=<?= urlencode((string) ($member['id'] ?? '')) ?>">Edit</a>
                                    <form action="process.php" method="POST" onsubmit="return confirm('Hapus member ini?')">
                                        <input type="hidden" name="action" value="delete_member">
                                        <input type="hidden" name="id" value="<?= htmlspecialchars((string) ($member['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                                        <button type="submit" class="btn-member-delete">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php endif; ?>

</section>
