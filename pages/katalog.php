
<?php

/**
 * E-BOOK LIBRARY
 * HALAMAN KATALOG E-BOOK
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

        $decodedBooks = json_decode($jsonContent, true);

        if (
            json_last_error() === JSON_ERROR_NONE &&
            is_array($decodedBooks)
        ) {
            $books = $decodedBooks;
        }
    }
}


// ==========================================
// 2. MENGAMBIL INPUT PENCARIAN
// ==========================================

$keyword = trim($_GET['search'] ?? '');
$selectedCategory = trim($_GET['category'] ?? '');


// ==========================================
// 3. MENGAMBIL DAFTAR KATEGORI
// ==========================================

$categories = [];

foreach ($books as $book) {

    $category = trim($book['category'] ?? '');

    if ($category !== '') {
        $categories[] = $category;
    }

}

$categories = array_values(array_unique($categories));

sort($categories);


// ==========================================
// 4. FILTER DATA BUKU
// ==========================================

$filteredBooks = array_filter($books, function ($book) use (
    $keyword,
    $selectedCategory
) {

    $title = $book['title'] ?? '';
    $author = $book['author'] ?? '';
    $publisher = $book['publisher'] ?? '';
    $category = $book['category'] ?? '';

    $matchesKeyword = true;
    $matchesCategory = true;

    // Pencarian berdasarkan judul, penulis, penerbit
    if ($keyword !== '') {

        $searchableText = $title . ' ' . $author . ' ' . $publisher;

        $matchesKeyword = stripos(
            $searchableText,
            $keyword
        ) !== false;

    }

    // Filter kategori
    if ($selectedCategory !== '') {

        $matchesCategory = (
            $category === $selectedCategory
        );

    }

    return $matchesKeyword && $matchesCategory;

});


// ==========================================
// 5. MENGHITUNG TOTAL HASIL
// ==========================================

$totalResults = count($filteredBooks);

?>

<section class="dashboard">

    <!-- PAGE HEADER -->
    <div class="page-header">

        <div>
            <h1>Katalog E-Book</h1>

            <p>
                Temukan dan jelajahi koleksi buku digital kami.
            </p>
        </div>

    </div>


    <!-- CATALOG CARD -->
    <section class="content-card catalog-card">

        <!-- CATALOG HEADING -->
        <div class="section-heading">

            <div>
                <h2>Daftar Koleksi E-Book</h2>

                <p>
                    Tersedia <?= number_format($totalResults) ?>
                    koleksi buku.
                </p>
            </div>

        </div>


        <!-- SEARCH AND FILTER -->
        <form method="GET"
              action="index.php"
              class="catalog-toolbar">

            <input type="hidden" name="page" value="katalog">

            <div class="search-box">

                <span class="search-icon">⌕</span>

                <input
                    type="search"
                    name="search"
                    placeholder="Cari judul, penulis, atau penerbit..."
                    value="<?= htmlspecialchars($keyword) ?>"
                    aria-label="Cari buku"
                >

            </div>


            <div class="filter-box">

                <select
                    name="category"
                    aria-label="Filter kategori"
                >

                    <option value="">Semua Kategori</option>

                    <?php foreach ($categories as $category): ?>

                        <option
                            value="<?= htmlspecialchars($category) ?>"
                            <?= $selectedCategory === $category ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars($category) ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <button type="submit" class="btn-primary">
                Cari Buku
            </button>


            <a href="index.php?page=katalog" class="btn-reset">
                Reset
            </a>

        </form>


        <!-- BOOK TABLE -->
        <div class="table-wrapper">

            <table class="catalog-table">

                <thead>

                    <tr>
                        <th>No</th>
                        <th>Judul</th>
                        <th>Penulis</th>
                        <th>Kategori</th>
                        <th>Stok</th>
                        <th>Aksi</th>
                    </tr>

                </thead>


                <tbody>

                    <?php if (empty($filteredBooks)): ?>

                        <tr>
                            <td colspan="6" class="empty-state">

                                <?php if (empty($books)): ?>

                                    Belum ada koleksi E-Book.

                                <?php else: ?>

                                    Buku yang kamu cari tidak ditemukan.

                                <?php endif; ?>

                            </td>
                        </tr>

                    <?php else: ?>

                        <?php foreach ($filteredBooks as $index => $book): ?>

                            <?php

                            $available = (int) (
                                $book['availableCopies'] ?? 0
                            );

                            ?>

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


                                <td>

                                    <?php if ($available > 0): ?>

                                        <span class="stock-badge available">
                                            <?= $available ?> Tersedia
                                        </span>

                                    <?php else: ?>

                                        <span class="stock-badge unavailable">
                                            Habis
                                        </span>

                                    <?php endif; ?>

                                </td>


                                <td>

                                    <a
                                        href="index.php?page=detail-ebook&id=<?= urlencode($book['id'] ?? '') ?>"
                                        class="btn-detail"
                                    >
                                        Detail
                                    </a>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </section>


    <!-- FOOTER INFORMATION -->
    <footer class="footer">

        <p>
            &copy; <?= date('Y') ?> E-Book Library.
            All Rights Reserved.
        </p>

    </footer>

</section>
