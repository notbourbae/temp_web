
<?php
// Data member akan dihubungkan dengan Backend.
// Untuk sementara, gunakan array kosong agar tidak menampilkan data dummy.
$members = [];

$search = trim($_GET['search'] ?? '');
$roleFilter = trim($_GET['role'] ?? '');

$filteredMembers = array_filter($members, function ($member) use ($search, $roleFilter) {
    $name = $member['name'] ?? '';
    $email = $member['email'] ?? '';
    $role = $member['role'] ?? '';

    $matchesSearch = $search === '' ||
        stripos($name, $search) !== false ||
        stripos($email, $search) !== false;

    $matchesRole = $roleFilter === '' || $role === $roleFilter;

    return $matchesSearch && $matchesRole;
});

$filteredMembers = array_values($filteredMembers);
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
                dari <?= count($members) ?> member.
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

        <select name="role" class="admin-member-filter">
            <option value="">Semua Role</option>
            <option value="member" <?= $roleFilter === 'member' ? 'selected' : '' ?>>
                Member
            </option>
            <option value="admin" <?= $roleFilter === 'admin' ? 'selected' : '' ?>>
                Admin
            </option>
        </select>

        <button type="submit" class="btn-primary">Cari</button>

        <a href="index.php?page=kelola-member" class="btn-reset">
            Reset
        </a>

    </form>

    <?php if (empty($filteredMembers)): ?>

        <div class="empty-state">
            <div class="empty-state-icon">👥</div>
            <h3>Belum Ada Data Member</h3>
            <p>
                Data anggota perpustakaan akan ditampilkan setelah
                terhubung dengan Backend.
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
                                    <button type="button" class="btn-member-edit" disabled>
                                        Edit
                                    </button>

                                    <button type="button" class="btn-member-delete" disabled>
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php endif; ?>

</section>
