
<?php

/**
 * E-BOOK LIBRARY
 * MEMBER DASHBOARD
 * Front-End: Rudi
 */

// ==========================================
// 1. MEMBACA FILE JSON
// ==========================================

function readDashboardJson($filePath)
{
    if (!file_exists($filePath)) {
        return [];
    }

    $content = file_get_contents($filePath);

    if ($content === false || trim($content) === '') {
        return [];
    }

    $data = json_decode($content, true);

    if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
        return [];
    }

    return $data;
}


// ==========================================
// 2. MENGAMBIL DATA BUKU
// ==========================================

$books = readDashboardJson(
    __DIR__ . '/../../data/ebooks.json'
);


// ==========================================
// 3. MENGAMBIL DATA TRANSAKSI
// ==========================================

$transactions = readDashboardJson(
    __DIR__ . '/../../data/transactions.json'
);


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
// 5. MENGHITUNG PEMINJAMAN AKTIF
// ==========================================

$totalBorrowed = 0;

$returnedStatuses = [
    'returned',
    'dikembalikan',
    'selesai',
    'completed'
];

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
        'title' => 'Dipinjam',
        'value' => $totalBorrowed,
        'icon' => '⇄',
        'color' => 'purple'
    ]
];


// ==========================================
// 7. MENGAMBIL 5 BUKU TERBARU
// ==========================================

// Mengikuti urutan data di ebooks.json.
// Belum ada atribut createdAt dalam skema buku.

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
