
<?php

/**
 * E-BOOK LIBRARY
 * DETAIL E-BOOK
 * Front-End: Rudi
 */

// ==========================================
// 1. MEMBACA DATA JSON
// ==========================================

$ebookFile = __DIR__ . '/../data/ebooks.json';

$books = [];

if (file_exists($ebookFile)) {

    $jsonContent = file_get_contents($ebookFile);

    if ($jsonContent !== false && trim($jsonContent) !== '') {

        $decoded = json_decode($jsonContent, true);

        if (
            json_last_error() === JSON_ERROR_NONE &&
            is_array($decoded)
        ) {
            $books = $decoded;
        }
    }
}


// ==========================================
// 2. MENGAMBIL ID BUKU DARI URL
// ==========================================

$bookId = $_GET['id'] ?? '';

$selectedBook = null;


// ==========================================
// 3. MENCARI BUKU BERDASARKAN ID
// ==========================================

foreach ($books as $book) {

    if (
        isset($book['id']) &&
        (string) $book['id'] === (string) $bookId
    ) {

        $selectedBook = $book;
        break;

    }

}


// ==========================================
// 4. FUNCTION FORMAT DATA
// ==========================================

function detailValue($value, $fallback = '-')
{
    if ($value === null || $value === '') {
        return $fallback;
    }

    return htmlspecialchars((string) $value);
}

?>

<section class="dashboard detail-page">

    <?php if ($selectedBook === null): ?>

        <!-- BUKU TIDAK DITEMUKAN -->

        <section class="content-card empty-detail">

            <div class="empty-detail-icon">▤</div>

            <h2>E-Book Tidak Ditemukan</h2>

            <p>
                Buku yang kamu cari tidak tersedia
                atau mungkin sudah dihapus.
            </p>

            <a href="index.php?page=katalog" class="btn-primary">
                Kembali ke Katalog
            </a>

        </section>

    <?php else: ?>

        <!-- DETAIL HEADER -->

        <div class="page-header">

            <div>

                <a href="index.php?page=katalog" class="back-link">
                    ← Kembali ke Katalog
                </a>

                <h1>Detail E-Book</h1>

                <p>Informasi lengkap koleksi buku.</p>

            </div>

        </div>


        <!-- DETAIL BOOK CARD -->

        <section class="content-card book-detail-card">

            <div class="book-detail-header">

                <div class="book-detail-symbol">
                    ▤
                </div>

                <div class="book-detail-information">

                    <span class="detail-label">INFORMASI E-BOOK</span>

                    <h2>
                        <?= detailValue($selectedBook['title'] ?? null) ?>
                    </h2>

                    <p>
                        <?= detailValue($selectedBook['author'] ?? null) ?>
                    </p>

                    <div class="detail-category">

                        <?= detailValue($selectedBook['category'] ?? null) ?>

                    </div>

                </div>

            </div>


            <!-- DESCRIPTION -->

            <div class="book-description">

                <h3>Deskripsi Buku</h3>

                <p>
                    <?= detailValue(
                        $selectedBook['description'] ?? null,
                        'Belum ada deskripsi buku.'
                    ) ?>
                </p>

            </div>


            <!-- BOOK ATTRIBUTES -->

            <div class="detail-section">

                <h3>Informasi Buku</h3>

                <div class="detail-grid">

                    <div class="detail-item">

                        <span>ID Buku</span>

                        <strong>
                            <?= detailValue($selectedBook['id'] ?? null) ?>
                        </strong>

                    </div>


                    <div class="detail-item">

                        <span>Judul Buku</span>

                        <strong>
                            <?= detailValue($selectedBook['title'] ?? null) ?>
                        </strong>

                    </div>


                    <div class="detail-item">

                        <span>Penulis</span>

                        <strong>
                            <?= detailValue($selectedBook['author'] ?? null) ?>
                        </strong>

                    </div>


                    <div class="detail-item">

                        <span>Penerbit</span>

                        <strong>
                            <?= detailValue($selectedBook['publisher'] ?? null) ?>
                        </strong>

                    </div>


                    <div class="detail-item">

                        <span>Kategori</span>

                        <strong>
                            <?= detailValue($selectedBook['category'] ?? null) ?>
                        </strong>

                    </div>


                    <div class="detail-item">

                        <span>ISBN</span>

                        <strong>
                            <?= detailValue($selectedBook['isbn'] ?? null) ?>
                        </strong>

                    </div>


                    <div class="detail-item">

                        <span>Tahun Terbit</span>

                        <strong>
                            <?= detailValue($selectedBook['publishedYear'] ?? null) ?>
                        </strong>

                    </div>


                    <div class="detail-item">

                        <span>Jumlah Halaman</span>

                        <strong>
                            <?= detailValue($selectedBook['pages'] ?? null) ?>
                        </strong>

                    </div>


                    <div class="detail-item">

                        <span>Bahasa</span>

                        <strong>
                            <?= detailValue($selectedBook['language'] ?? null) ?>
                        </strong>

                    </div>


                    <div class="detail-item">

                        <span>Format</span>

                        <strong>
                            <?= detailValue($selectedBook['format'] ?? null) ?>
                        </strong>

                    </div>


                    <div class="detail-item">

                        <span>Total Salinan</span>

                        <strong>
                            <?= detailValue($selectedBook['totalCopies'] ?? null) ?>
                        </strong>

                    </div>


                    <div class="detail-item">

                        <span>Salinan Tersedia</span>

                        <strong>
                            <?= detailValue($selectedBook['availableCopies'] ?? null) ?>
                        </strong>

                    </div>

                </div>

            </div>


            <!-- BOOK AVAILABILITY -->

            <div class="book-availability">

                <div>

                    <span>Status Ketersediaan</span>

                    <?php if (
                        (int) ($selectedBook['availableCopies'] ?? 0) > 0
                    ): ?>

                        <span class="stock-badge available">
                            Tersedia
                        </span>

                    <?php else: ?>

                        <span class="stock-badge unavailable">
                            Tidak Tersedia
                        </span>

                    <?php endif; ?>

                </div>

                <a
                    href="index.php?page=peminjaman&id=<?= urlencode($selectedBook['id']) ?>"
                    class="btn-primary"
                >
                    Pinjam E-Book
                </a>

            </div>

        </section>

    <?php endif; ?>

</section>
