
<?php
$errors = $_SESSION['ebook_errors'] ?? [];
$oldInput = $_SESSION['ebook_old_input'] ?? [];

unset($_SESSION['ebook_errors'], $_SESSION['ebook_old_input']);

function ebookFormValue($key, $oldInput)
{
    return htmlspecialchars($oldInput[$key] ?? '', ENT_QUOTES, 'UTF-8');
}
?>

<section class="page-header">
    <div>
        <h1>Tambah E-Book</h1>
        <p>Masukkan informasi e-book baru ke koleksi perpustakaan.</p>
    </div>

    <a href="index.php?page=kelola-ebook" class="btn-reset">
        Kembali
    </a>
</section>

<section class="content-card ebook-form-card">

    <div class="content-card-header">
        <div>
            <h2>Informasi E-Book</h2>
            <p>Lengkapi seluruh informasi buku berikut.</p>
        </div>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="form-error-message">
            <strong>Periksa kembali formulir:</strong>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="process.php" method="POST" class="ebook-form">

        <input type="hidden" name="action" value="add-ebook">

        <div class="ebook-form-grid">

            <div class="form-group">
                <label for="title">Judul E-Book *</label>
                <input
                    type="text"
                    id="title"
                    name="title"
                    value="<?= ebookFormValue('title', $oldInput) ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="author">Penulis *</label>
                <input
                    type="text"
                    id="author"
                    name="author"
                    value="<?= ebookFormValue('author', $oldInput) ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="publisher">Penerbit *</label>
                <input
                    type="text"
                    id="publisher"
                    name="publisher"
                    value="<?= ebookFormValue('publisher', $oldInput) ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="category">Kategori *</label>
                <input
                    type="text"
                    id="category"
                    name="category"
                    value="<?= ebookFormValue('category', $oldInput) ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="isbn">ISBN *</label>
                <input
                    type="text"
                    id="isbn"
                    name="isbn"
                    value="<?= ebookFormValue('isbn', $oldInput) ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="publishedYear">Tahun Terbit *</label>
                <input
                    type="number"
                    id="publishedYear"
                    name="publishedYear"
                    min="1000"
                    max="<?= date('Y') ?>"
                    value="<?= ebookFormValue('publishedYear', $oldInput) ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="pages">Jumlah Halaman *</label>
                <input
                    type="number"
                    id="pages"
                    name="pages"
                    min="1"
                    value="<?= ebookFormValue('pages', $oldInput) ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="language">Bahasa *</label>
                <select id="language" name="language" required>
                    <option value="">Pilih Bahasa</option>
                    <option value="Indonesia"
                        <?= ($oldInput['language'] ?? '') === 'Indonesia' ? 'selected' : '' ?>>
                        Indonesia
                    </option>
                    <option value="Inggris"
                        <?= ($oldInput['language'] ?? '') === 'Inggris' ? 'selected' : '' ?>>
                        Inggris
                    </option>
                    <option value="Lainnya"
                        <?= ($oldInput['language'] ?? '') === 'Lainnya' ? 'selected' : '' ?>>
                        Lainnya
                    </option>
                </select>
            </div>

            <div class="form-group">
                <label for="format">Format E-Book *</label>
                <select id="format" name="format" required>
                    <option value="">Pilih Format</option>
                    <option value="PDF"
                        <?= ($oldInput['format'] ?? '') === 'PDF' ? 'selected' : '' ?>>
                        PDF
                    </option>
                    <option value="EPUB"
                        <?= ($oldInput['format'] ?? '') === 'EPUB' ? 'selected' : '' ?>>
                        EPUB
                    </option>
                </select>
            </div>

            <div class="form-group">
                <label for="totalCopies">Total Salinan *</label>
                <input
                    type="number"
                    id="totalCopies"
                    name="totalCopies"
                    min="1"
                    value="<?= ebookFormValue('totalCopies', $oldInput) ?>"
                    required
                >
            </div>

        </div>

        <div class="form-group form-description">
            <label for="description">Deskripsi E-Book *</label>
            <textarea
                id="description"
                name="description"
                rows="5"
                required
            ><?= ebookFormValue('description', $oldInput) ?></textarea>
        </div>

        <div class="ebook-form-actions">
            <a href="index.php?page=kelola-ebook" class="btn-reset">
                Batal
            </a>

            <button type="submit" class="btn-primary">
                Simpan E-Book
            </button>
        </div>

    </form>

</section>
