
<?php
require_once __DIR__ . '/../../src/autoload.php';

$ebookService = new \App\Services\EbookService();
$ebookId = trim($_GET['id'] ?? '');
$ebook = $ebookId === '' ? null : $ebookService->getById($ebookId);
$isEditing = $ebookId !== '';
$formData = array_merge($ebook ?? [], $_SESSION['ebook_old_input'] ?? []);
unset($_SESSION['ebook_old_input']);

function ebookFormValue(string $key, array $formData): string
{
    return htmlspecialchars((string) ($formData[$key] ?? ''), ENT_QUOTES, 'UTF-8');
}
?>

<?php if ($isEditing && $ebook === null): ?>
    <section class="content-card">
        <h1>E-Book tidak ditemukan</h1>
        <a href="index.php?page=kelola-ebook" class="btn-primary">Kembali ke daftar</a>
    </section>
    <?php return; ?>
<?php endif; ?>

<section class="page-header">
    <div>
        <h1><?= $isEditing ? 'Edit E-Book' : 'Tambah E-Book' ?></h1>
        <p><?= $isEditing ? 'Perbarui informasi e-book.' : 'Masukkan informasi e-book baru ke koleksi perpustakaan.' ?></p>
    </div>

    <a href="index.php?page=kelola-ebook" class="btn-reset">
        Kembali
    </a>
</section>

<section class="content-card ebook-form-card">

    <div class="content-card-header">
        <div>
            <h2><?= $isEditing ? 'Perbarui Informasi E-Book' : 'Informasi E-Book' ?></h2>
            <p>Lengkapi seluruh informasi buku berikut.</p>
        </div>
    </div>

    <form action="process.php" method="POST" class="ebook-form">

        <input type="hidden" name="action" value="<?= $isEditing ? 'update_ebook' : 'create_ebook' ?>">
        <?php if ($isEditing): ?>
            <input type="hidden" name="id" value="<?= htmlspecialchars($ebookId, ENT_QUOTES, 'UTF-8') ?>">
        <?php endif; ?>

        <div class="ebook-form-grid">

            <div class="form-group">
                <label for="title">Judul E-Book *</label>
                <input
                    type="text"
                    id="title"
                    name="title"
                    value="<?= ebookFormValue('title', $formData) ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="author">Penulis *</label>
                <input
                    type="text"
                    id="author"
                    name="author"
                    value="<?= ebookFormValue('author', $formData) ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="publisher">Penerbit *</label>
                <input
                    type="text"
                    id="publisher"
                    name="publisher"
                    value="<?= ebookFormValue('publisher', $formData) ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="category">Kategori *</label>
                <input
                    type="text"
                    id="category"
                    name="category"
                    value="<?= ebookFormValue('category', $formData) ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="isbn">ISBN *</label>
                <input
                    type="text"
                    id="isbn"
                    name="isbn"
                    value="<?= ebookFormValue('isbn', $formData) ?>"
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
                    value="<?= ebookFormValue('publishedYear', $formData) ?>"
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
                    value="<?= ebookFormValue('pages', $formData) ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="language">Bahasa *</label>
                <input
                    type="text"
                    id="language"
                    name="language"
                    value="<?= ebookFormValue('language', $formData) ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="format">Format E-Book *</label>
                <select id="format" name="format" required>
                    <option value="">Pilih Format</option>
                    <option value="PDF"
                        <?= ($formData['format'] ?? '') === 'PDF' ? 'selected' : '' ?>>
                        PDF
                    </option>
                    <option value="EPUB"
                        <?= ($formData['format'] ?? '') === 'EPUB' ? 'selected' : '' ?>>
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
                    value="<?= ebookFormValue('totalCopies', $formData) ?>"
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
            ><?= ebookFormValue('description', $formData) ?></textarea>
        </div>

        <div class="ebook-form-actions">
            <a href="index.php?page=kelola-ebook" class="btn-reset">
                Batal
            </a>

            <button type="submit" class="btn-primary">
                <?= $isEditing ? 'Simpan Perubahan' : 'Simpan E-Book' ?>
            </button>
        </div>

    </form>

</section>
