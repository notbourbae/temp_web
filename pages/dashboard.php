<?php

/**
 * E-BOOK LIBRARY
 * MEMBER DASHBOARD
 * Front-End: Rudi
 */

// [DITAMBAHKAN] Memuat autoload, EbookService, dan TransactionService untuk statistik dari JSON
require_once __DIR__ . '/../src/autoload.php';

$ebookService       = new \App\Services\EbookService();
$transactionService = new \App\Services\TransactionService();


// ==========================================
// 2. MENGAMBIL DATA BUKU
// ==========================================

// [DIUBAH] Mengambil data E-Book dari JSON melalui EbookService
$books = $ebookService->getAll();


// ==========================================
// 3. MENGAMBIL DATA TRANSAKSI PENGGUNA
// ==========================================

$currentUserId = (string) ($_SESSION['user']['id'] ?? $_SESSION['user']['userId'] ?? '');

// [DIUBAH] Mengambil transaksi khusus pengguna dari transactions.json (atau semua jika belum ada sesi)
$transactions = $currentUserId !== ''
    ? $transactionService->getByUserId($currentUserId)
    : $transactionService->getAll();


// ==========================================
// 4. MENGHITUNG STATISTIK BUKU
// ==========================================

$totalBooks = count($books);

$availableCopies = 0;

foreach ($books as $book) {

    $availableCopies += (int) (
        $book['availableCopies'] ?? 0
    );

}


// ==========================================
// 5. MENGHITUNG TOTAL PEMINJAMAN & AKTIF
// ==========================================

// [DIUBAH] Menghitung total seluruh peminjaman pengguna dari JSON
$totalPeminjaman = count($transactions);
$totalBorrowed   = 0;

$activeStatuses = [
    'borrowed',
    'dipinjam',
    'active',
    'aktif'
];

foreach ($transactions as $transaction) {

    $status = strtolower(
        trim($transaction['status'] ?? '')
    );

    $returnedAt = $transaction['returnedAt'] ?? null;

    if (
        in_array($status, $activeStatuses, true) &&
        $returnedAt === null
    ) {
        $totalBorrowed++;
    }

}


// ==========================================
// 6. DATA STATISTIK
// ==========================================

$statistics = [
    [
        'title' => 'Total E-Book',
        'value' => $totalBooks,
        'icon' => '▤',
        'color' => 'orange'
    ],
    [
        'title' => 'Tersedia',
        'value' => $availableCopies,
        'icon' => '✓',
        'color' => 'green'
    ],
    [
        // [DIUBAH] Menampilkan Total Peminjaman dari JSON (beserta jumlah aktif di sub-teks)
        'title' => 'Total Peminjaman (' . $totalBorrowed . ' Aktif)',
        'value' => $totalPeminjaman,
        'icon' => '⇄',
        'color' => 'purple'
    ]
];


// ==========================================
// 7. MENGAMBIL 5 BUKU & TRANSAKSI TERBARU
// ==========================================

// Pemetaan judul buku berdasarkan ID untuk tabel transaksi terbaru
$ebookTitleMap = [];
foreach ($books as $book) {
    if (isset($book['id'])) {
        $ebookTitleMap[(string) $book['id']] = $book['title'] ?? '-';
    }
}

// [DITAMBAHKAN] Mengurutkan dan mengambil 5 data transaksi terbaru dari JSON
$recentTransactions = $transactions;
usort($recentTransactions, static function ($a, $b) {
    return strtotime($b['borrowedAt'] ?? '') <=> strtotime($a['borrowedAt'] ?? '');
});
$recentTransactions = array_slice($recentTransactions, 0, 5);

$recentBooks = array_slice($books, 0, 5);


// ==========================================
// 8. NAMA PENGGUNA
// ==========================================

$userName = $_SESSION['user']['name'] ?? 'Pengguna';

?>

<!-- ======================================
     DASHBOARD CONTENT
======================================= -->

<section class="dashboard">

    <!-- WELCOME SECTION -->
    <div class="welcome-section">

        <div>

            <h1>Dashboard</h1>

            <p>
                Halo,
                <strong>
                    <?= htmlspecialchars($userName) ?>
                </strong>
                👋
            </p>

            <span>
                Selamat datang di E-Book Library
            </span>

        </div>

    </div>


    <!-- ==================================
         STATISTICS
    =================================== -->

    <section class="statistics"
             aria-label="Statistik perpustakaan">

        <?php foreach ($statistics as $stat): ?>

            <article class="stat-card">

                <div class="stat-icon <?= htmlspecialchars($stat['color']) ?>">

                    <?= htmlspecialchars($stat['icon']) ?>

                </div>

                <div class="stat-information">

                    <h2>
                        <?= number_format($stat['value']) ?>
                    </h2>

                    <p>
                        <?= htmlspecialchars($stat['title']) ?>
                    </p>

                </div>

            </article>

        <?php endforeach; ?>

    </section>


    <!-- ==================================
         [DITAMBAHKAN] RECENT TRANSACTIONS
    =================================== -->

    <section class="content-card">

        <div class="section-heading">

            <div>

                <h2>Transaksi Terbaru</h2>

                <p>
                    Riwayat peminjaman e-book terbaru Anda
                </p>

            </div>

            <a href="index.php?page=riwayat" class="see-all">
                Lihat Semua →
            </a>

        </div>


        <div class="table-wrapper">

            <table>

                <thead>

                    <tr>
                        <th>No</th>
                        <th>Judul Buku</th>
                        <th>Tanggal Pinjam</th>
                        <th>Jatuh Tempo</th>
                        <th>Status</th>
                    </tr>

                </thead>


                <tbody>

                    <?php if (empty($recentTransactions)): ?>

                        <tr>

                            <td colspan="5" class="empty-state">

                                Belum ada transaksi peminjaman.

                            </td>

                        </tr>

                    <?php else: ?>

                        <?php foreach ($recentTransactions as $index => $trx): ?>

                            <?php
                            $isReturned = !empty($trx['returnedAt']) || strtolower((string) ($trx['status'] ?? '')) === 'returned';
                            $bookTitle  = $ebookTitleMap[(string) ($trx['ebookId'] ?? '')] ?? ($trx['ebookId'] ?? '-');
                            ?>

                            <tr>

                                <td>
                                    <?= $index + 1 ?>
                                </td>

                                <td class="book-title">
                                    <?= htmlspecialchars((string) $bookTitle) ?>
                                </td>

                                <td>
                                    <?= !empty($trx['borrowedAt']) ? date('d/m/Y', strtotime((string) $trx['borrowedAt'])) : '-' ?>
                                </td>

                                <td>
                                    <?= !empty($trx['dueAt']) ? date('d/m/Y', strtotime((string) $trx['dueAt'])) : '-' ?>
                                </td>

                                <td>
                                    <?php if ($isReturned): ?>
                                        <span class="status-badge history-returned">Dikembalikan</span>
                                    <?php else: ?>
                                        <span class="status-badge history-active">Sedang Dipinjam</span>
                                    <?php endif; ?>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </section>


    <!-- ==================================
         RECENT BOOKS
    =================================== -->

    <section class="content-card">

        <div class="section-heading">

            <div>

                <h2>E-Book Terbaru</h2>

                <p>
                    Koleksi buku terbaru di perpustakaan
                </p>

            </div>

            <a href="index.php?page=katalog" class="see-all">
                Lihat Semua →
            </a>

        </div>


        <div class="table-wrapper">

            <table>

                <thead>

                    <tr>
                        <th>No</th>
                        <th>Judul Buku</th>
                        <th>Penulis</th>
                        <th>Kategori</th>
                    </tr>

                </thead>


                <tbody>

                    <?php if (empty($recentBooks)): ?>

                        <tr>

                            <td colspan="4" class="empty-state">

                                Belum ada koleksi E-Book.

                            </td>

                        </tr>

                    <?php else: ?>

                        <?php foreach ($recentBooks as $index => $book): ?>

                            <tr>

                                <td>
                                    <?= $index + 1 ?>
                                </td>

                                <td class="book-title">

                                    <?= htmlspecialchars(
                                        $book['title'] ?? '-'
                                    ) ?>

                                </td>

                                <td>

                                    <?= htmlspecialchars(
                                        $book['author'] ?? '-'
                                    ) ?>

                                </td>

                                <td>

                                    <span class="category-badge">

                                        <?= htmlspecialchars(
                                            $book['category'] ?? '-'
                                        ) ?>

                                    </span>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </section>


    <!-- ==================================
         INFORMATION
    =================================== -->

    <section class="content-card information-card">

        <div class="section-heading">

            <div>

                <h2>Informasi Perpustakaan</h2>

                <p>
                    Layanan E-Book Library
                </p>

            </div>

        </div>


        <div class="information-content">

            <div class="information-icon">
                ▤
            </div>

            <div>

                <h3>Selamat Membaca!</h3>

                <p>
                    Temukan berbagai koleksi buku digital
                    dari berbagai kategori. Nikmati kemudahan
                    membaca dan meminjam E-Book melalui
                    E-Book Library.
                </p>

            </div>

        </div>

    </section>


    <!-- ==================================
         FOOTER
    =================================== -->

    <footer class="footer">

        <p>
            &copy; <?= date('Y') ?> E-Book Library.
            All Rights Reserved.
        </p>

    </footer>

</section>