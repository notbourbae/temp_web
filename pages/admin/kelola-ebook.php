
<?php
function readKelolaEbookJson($filePath)
{
    if (!file_exists($filePath)) {
        return [];
    }

    $content = file_get_contents($filePath);

    if ($content === false || trim($content) === '') {
        return [];
    }

    $data = json_decode($content, true);

    return is_array($data) ? $data : [];
}

$ebooks = readKelolaEbookJson(__DIR__ . '/../../data/ebooks.json');

$search = trim($_GET['search'] ?? '');
$categoryFilter = trim($_GET['category'] ?? '');

// Mengambil kategori yang tersedia
$categories = [];

foreach ($ebooks as $ebook) {
    $category = trim($ebook['category'] ?? '');

    if ($category !== '') {
        $categories[] = $category;
    }
}

$categories = array_values(array_unique($categories));

// Filter e-book
$filteredEbooks = array_filter($ebooks, function ($ebook) use ($search, $categoryFilter) {
    $title = $ebook['title'] ?? '';
    $author = $ebook['author'] ?? '';
    $publisher = $ebook['publisher'] ?? '';
    $category = $ebook['category'] ?? '';

    $matchesSearch = $search === '' ||
        stripos($title, $search) !== false ||
        stripos($author, $search) !== false ||
        stripos($publisher, $search) !== false;

    $matchesCategory = $categoryFilter === '' ||
        $category === $categoryFilter;

    return $matchesSearch && $matchesCategory;
});

$filteredEbooks = array_values($filteredEbooks);
?>

<section class="page-header admin-book-header">
    <div>
        <h1>Kelola E-Book</h1>
        <p>Kelola koleksi e-book perpustakaan.</p>
    </div>

    <a href="index.php?page=tambah-ebook" class="btn-primary">
        + Tambah E-Book
    </a>
</section>

<section class="content-card admin-book-content">

    <div class="content-card-header">
        <div>
            <h2>Daftar Koleksi E-Book</h2>
            <p>
                Menampilkan <?= count($filteredEbooks) ?>
                dari <?= count($ebooks) ?> e-book.
            </p>
        </div>
    </div>

    <form method="GET" action="index.php" class="admin-book-toolbar">

        <input type="hidden" name="page" value="kelola-ebook">

        <div class="admin-book-search">
            <input
                type="text"
                name="search"
                placeholder="Cari judul, penulis, atau penerbit..."
                value="<?= htmlspecialchars($search) ?>"
            >
        </div>

        <select name="category" class="admin-book-filter">
            <option value="">Semua Kategori</option>

            <?php foreach ($categories as $category): ?>
                <option
                    value="<?= htmlspecialchars($category) ?>"
                    <?= $categoryFilter === $category ? 'selected' : '' ?>
                >
                    <?= htmlspecialchars($category) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" class="btn-primary">
            Cari
        </button>

        <a href="index.php?page=kelola-ebook" class="btn-reset">
            Reset
        </a>

    </form>

    <?php if (empty($filteredEbooks)): ?>

        <div class="empty-state">
            <div class="empty-state-icon">📚</div>
            <h3>
                <?= empty($ebooks) ? 'Belum Ada E-Book' : 'E-Book Tidak Ditemukan' ?>
            </h3>
            <p>
                <?= empty($ebooks)
                    ? 'Koleksi e-book akan muncul setelah data ditambahkan.'
                    : 'Coba gunakan kata kunci atau kategori lainnya.' ?>
            </p>
        </div>

    <?php else: ?>

        <div class="table-wrapper">
            <table class="admin-book-table">

                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Judul E-Book</th>
                        <th>Penulis</th>
                        <th>Kategori</th>
                        <th>Stok</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($filteredEbooks as $index => $ebook): ?>

                        <?php
                        $available = (int) ($ebook['availableCopies'] ?? 0);
                        ?>

                        <tr>
                            <td><?= $index + 1 ?></td>

                            <td>
                                <strong>
                                    <?= htmlspecialchars($ebook['title'] ?? '-') ?>
                                </strong>
                            </td>

                            <td>
                                <?= htmlspecialchars($ebook['author'] ?? '-') ?>
                            </td>

                            <td>
                                <span class="category-badge">
                                    <?= htmlspecialchars($ebook['category'] ?? '-') ?>
                                </span>
                            </td>

                            <td><?= $available ?></td>

                            <td>
                                <?php if ($available > 0): ?>
                                    <span class="status-badge history-returned">
                                        Tersedia
                                    </span>
                                <?php else: ?>
                                    <span class="status-badge history-overdue">
                                        Habis
                                    </span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <div class="admin-book-actions">
                                    <a
                                        href="index.php?page=detail-ebook&id=<?= urlencode((string) ($ebook['id'] ?? '')) ?>"
                                        class="btn-action btn-view"
                                    >
                                        Detail
                                    </a>
                                </div>
                            </td>
                        </tr>

                    <?php endforeach; ?>
                </tbody>

            </table>
        </div>

    <?php endif; ?>

</section>
