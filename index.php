
<?php

/**
 * E-BOOK LIBRARY
 * MAIN APPLICATION ROUTER
 * Front-End: Rudi
 */

// ==========================================
// 1. SESSION
// ==========================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// ==========================================
// 2. DAFTAR HALAMAN
// ==========================================

$pages = [

    '404' => [
    'title' => 'Halaman Tidak Ditemukan',
    'file' => 'pages/errors/404.php',
    'auth' => false
    ],
    
    'tambah-member' => [
        'title' => 'Tambah Member',
        'file' => 'pages/admin/tambah-member.php',
        'auth' => false
    ],

    'tambah-ebook' => [
        'title' => 'Tambah E-Book',
        'file' => 'pages/admin/tambah-ebook.php',
        'auth' => false
    ],
    // Authentication
    'login' => [
        'title' => 'Login',
        'file' => 'pages/login.php',
        'auth' => true
    ],

    'register' => [
        'title' => 'Register',
        'file' => 'pages/register.php',
        'auth' => true
    ],

    // Member
    'dashboard' => [
        'title' => 'Dashboard',
        'file' => 'pages/dashboard.php',
        'auth' => false
    ],

    'katalog' => [
        'title' => 'Katalog E-Book',
        'file' => 'pages/katalog.php',
        'auth' => false
    ],

    'detail-ebook' => [
        'title' => 'Detail E-Book',
        'file' => 'pages/detail-ebook.php',
        'auth' => false
    ],

    'peminjaman' => [
        'title' => 'Peminjaman Saya',
        'file' => 'pages/peminjaman.php',
        'auth' => false
    ],

    'riwayat' => [
        'title' => 'Riwayat Peminjaman',
        'file' => 'pages/riwayat.php',
        'auth' => false
    ],

    'profil' => [
        'title' => 'Profil Saya',
        'file' => 'pages/profil.php',
        'auth' => false
    ],

    // Admin
    'admin-dashboard' => [
        'title' => 'Dashboard Admin',
        'file' => 'pages/admin/dashboard.php',
        'auth' => false
    ],

    'kelola-ebook' => [
        'title' => 'Kelola E-Book',
        'file' => 'pages/admin/kelola-ebook.php',
        'auth' => false
    ],

    'kelola-member' => [
        'title' => 'Kelola Member',
        'file' => 'pages/admin/kelola-member.php',
        'auth' => false
    ]

];


// ==========================================
// 3. MENENTUKAN HALAMAN
// ==========================================

$currentPage = $_GET['page'] ?? 'dashboard';


// ==========================================
// 4. VALIDASI HALAMAN
// ==========================================

if (isset($pages[$currentPage])) {

    $pageTitle = $pages[$currentPage]['title'];

    $pageFile = $pages[$currentPage]['file'];

    $isAuthPage = $pages[$currentPage]['auth'];

} else {

    http_response_code(404);

    $pageTitle = 'Halaman Tidak Ditemukan';

    $pageFile = 'pages/errors/404.php';

    $isAuthPage = false;

}


// ==========================================
// 5. VALIDASI FILE
// ==========================================

$fullPagePath = __DIR__ . '/' . $pageFile;

if (!file_exists($fullPagePath)) {

    http_response_code(404);

    $pageTitle = 'Halaman Tidak Ditemukan';

    $fullPagePath = __DIR__ . '/pages/errors/404.php';

    $isAuthPage = false;

}


// ==========================================
// 6. RENDER HALAMAN
// ==========================================

if ($isAuthPage) {

    // Login dan Register
    require $fullPagePath;

} else {

    // Header
    require __DIR__ . '/includes/header.php';

    // Sidebar
    require __DIR__ . '/includes/sidebar.php';

    // Konten halaman
    echo '<main class="main-content">';

    require $fullPagePath;

    echo '</main>';

    // Footer
    require __DIR__ . '/includes/footer.php';

}

?>
