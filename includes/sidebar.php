
<?php

/**
 * E-BOOK LIBRARY
 * Sidebar Navigation
 */

$currentPage = $_GET['page'] ?? 'dashboard';

// Membaca role dari URL untuk simulasi Front-End
$userRole = $_GET['role'] ?? 'member';

// Memastikan role hanya admin atau member
if (!in_array($userRole, ['admin', 'member'], true)) {
    $userRole = 'member';
}

// Membuat URL navigasi dengan role
function sidebarLink($page, $role) {
    return "index.php?page=" . $page . "&role=" . $role;
}

// Menentukan menu aktif
function isActivePage($page, $currentPage) {
    return $page === $currentPage ? 'active' : '';
}

?>

<aside class="sidebar" id="sidebar">

    <!-- BRAND -->
    <div class="brand">

        <div class="brand-icon">▤</div>

        <div class="brand-info">
            <h2>E-Book Library</h2>
            <p>Baca, Pinjam, Kelola</p>
        </div>

    </div>

    <!-- NAVIGATION -->
    <nav class="sidebar-nav" aria-label="Navigasi utama">

        <?php if ($userRole === 'admin'): ?>

            <!-- ADMIN MENU -->

            <p class="nav-label">MENU ADMIN</p>

            <a href="<?= sidebarLink('admin-dashboard', $userRole) ?>"
               class="nav-link <?= isActivePage('admin-dashboard', $currentPage) ?>">
                <span class="nav-icon">▦</span>
                <span>Dashboard Admin</span>
            </a>

            <a href="<?= sidebarLink('kelola-ebook', $userRole) ?>"
               class="nav-link <?= isActivePage('kelola-ebook', $currentPage) ?>">
                <span class="nav-icon">▤</span>
                <span>Kelola E-Book</span>
            </a>

            <a href="<?= sidebarLink('tambah-ebook', $userRole) ?>"
               class="nav-link <?= isActivePage('tambah-ebook', $currentPage) ?>">
                <span class="nav-icon">+</span>
                <span>Tambah E-Book</span>
            </a>

            <a href="<?= sidebarLink('kelola-member', $userRole) ?>"
               class="nav-link <?= isActivePage('kelola-member', $currentPage) ?>">
                <span class="nav-icon">♙</span>
                <span>Kelola Member</span>
            </a>

            <a href="<?= sidebarLink('tambah-member', $userRole) ?>"
               class="nav-link <?= isActivePage('tambah-member', $currentPage) ?>">
                <span class="nav-icon">+</span>
                <span>Tambah Member</span>
            </a>

        <?php else: ?>

            <!-- MEMBER MENU -->

            <p class="nav-label">MENU UTAMA</p>

            <a href="<?= sidebarLink('dashboard', $userRole) ?>"
               class="nav-link <?= isActivePage('dashboard', $currentPage) ?>">
                <span class="nav-icon">▦</span>
                <span>Dashboard</span>
            </a>

            <a href="<?= sidebarLink('katalog', $userRole) ?>"
               class="nav-link <?= isActivePage('katalog', $currentPage) ?>">
                <span class="nav-icon">▤</span>
                <span>Katalog E-Book</span>
            </a>

            <a href="<?= sidebarLink('peminjaman', $userRole) ?>"
               class="nav-link <?= isActivePage('peminjaman', $currentPage) ?>">
                <span class="nav-icon">⇄</span>
                <span>Peminjaman</span>
            </a>

            <a href="<?= sidebarLink('riwayat', $userRole) ?>"
               class="nav-link <?= isActivePage('riwayat', $currentPage) ?>">
                <span class="nav-icon">◷</span>
                <span>Riwayat</span>
            </a>

            <a href="<?= sidebarLink('profil', $userRole) ?>"
               class="nav-link <?= isActivePage('profil', $currentPage) ?>">
                <span class="nav-icon">♙</span>
                <span>Profil</span>
            </a>

        <?php endif; ?>

    </nav>

    <!-- SIDEBAR FOOTER -->
    <div class="sidebar-footer">

        <a href="index.php?page=login" class="nav-link logout">
            <span class="nav-icon">↪</span>
            <span>Keluar</span>
        </a>

    </div>

</aside>
