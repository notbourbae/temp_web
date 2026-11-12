
<?php
function readPeminjamanJson($filePath)
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

$ebooks = readPeminjamanJson(__DIR__ . '/../data/ebooks.json');
$transactions = readPeminjamanJson(__DIR__ . '/../data/transactions.json');

// Identitas pengguna yang sedang login
$currentUser = $_SESSION['user'] ?? [];
$currentUserId = $currentUser['id'] ?? $currentUser['userId'] ?? null;

// Membuat daftar e-book berdasarkan ID
$ebookMap = [];

foreach ($ebooks as $ebook) {
    if (isset($ebook['id'])) {
        $ebookMap[(string) $ebook['id']] = $ebook;
    }
}

// Mengambil transaksi milik pengguna yang sedang login
$myLoans = [];

if ($currentUserId !== null) {
    foreach ($transactions as $transaction) {
        if (
            isset($transaction['userId']) &&
            (string) $transaction['userId'] === (string) $currentUserId
        ) {
            $status = strtolower($transaction['status'] ?? '');

            if (
                in_array($status, ['borrowed', 'dipinjam', 'active', 'aktif']) &&
                empty($transaction['returnedAt'])
            ) {
                $myLoans[] = $transaction;
            }
        }
    }
}

function peminjamanDate($date)
{
    if (empty($date)) {
        return '-';
    }

    $timestamp = strtotime($date);

    return $timestamp ? date('d/m/Y', $timestamp) : '-';
}
?>

<section class="page-header">
    <div>
        <h1>Peminjaman Saya</h1>
        <p>Daftar e-book yang sedang kamu pinjam.</p>
    </div>
</section>

<section class="loan-summary">
    <div class="loan-summary-card">
        <span class="loan-summary-icon">↗</span>
        <div>
            <p>Sedang Dipinjam</p>
            <h2><?= count($myLoans) ?></h2>
        </div>
    </div>
</section>

<section class="content-card loan-content">
    <div class="content-card-header">
        <div>
            <h2>Daftar Peminjaman</h2>
            <p>Informasi e-book yang belum dikembalikan.</p>
        </div>
    </div>

    <?php if ($currentUserId === null): ?>

        <div class="empty-state">
            <div class="empty-state-icon">♙</div>
            <h3>Identitas pengguna belum tersedia</h3>
            <p>Silakan login terlebih dahulu untuk melihat peminjaman.</p>
        </div>

    <?php elseif (empty($myLoans)): ?>

        <div class="empty-state">
            <div class="empty-state-icon">📚</div>
            <h3>Belum Ada Peminjaman</h3>
            <p>Kamu belum memiliki e-book yang sedang dipinjam.</p>
            <a href="index.php?page=katalog" class="btn-primary">
                Jelajahi Katalog
            </a>
        </div>

    <?php else: ?>

        <div class="table-wrapper">
            <table class="loan-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Judul E-Book</th>
                        <th>Tanggal Pinjam</th>
                        <th>Jatuh Tempo</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($myLoans as $index => $loan): ?>
                        <?php
                        $ebookId = (string) ($loan['ebookId'] ?? '');
                        $ebook = $ebookMap[$ebookId] ?? null;
                        ?>

                        <tr>
                            <td><?= $index + 1 ?></td>

                            <td>
                                <strong>
                                    <?= htmlspecialchars(
                                        $ebook['title'] ?? 'E-Book tidak ditemukan'
                                    ) ?>
                                </strong>
                                <small>
                                    <?= htmlspecialchars($ebook['author'] ?? '-') ?>
                                </small>
                            </td>

                            <td>
                                <?= peminjamanDate($loan['borrowedAt'] ?? null) ?>
                            </td>

                            <td>
                                <?= peminjamanDate($loan['dueAt'] ?? null) ?>
                            </td>

                            <td>
                                <span class="status-badge status-active">
                                    Sedang Dipinjam
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php endif; ?>
</section>
