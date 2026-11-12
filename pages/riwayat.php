
<?php
function readRiwayatJson($filePath)
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

$ebooks = readRiwayatJson(__DIR__ . '/../data/ebooks.json');
$transactions = readRiwayatJson(__DIR__ . '/../data/transactions.json');

$currentUser = $_SESSION['user'] ?? [];
$currentUserId = $currentUser['id'] ?? $currentUser['userId'] ?? null;

// Membuat pemetaan e-book berdasarkan ID
$ebookMap = [];

foreach ($ebooks as $ebook) {
    if (isset($ebook['id'])) {
        $ebookMap[(string) $ebook['id']] = $ebook;
    }
}

// Mengambil semua transaksi milik pengguna
$myHistory = [];

if ($currentUserId !== null) {
    foreach ($transactions as $transaction) {
        if (
            isset($transaction['userId']) &&
            (string) $transaction['userId'] === (string) $currentUserId
        ) {
            $myHistory[] = $transaction;
        }
    }
}

// Mengurutkan transaksi berdasarkan tanggal terbaru
usort($myHistory, function ($a, $b) {
    return strtotime($b['borrowedAt'] ?? '') <=> strtotime($a['borrowedAt'] ?? '');
});

function riwayatDate($date)
{
    if (empty($date)) {
        return '-';
    }

    $timestamp = strtotime($date);

    return $timestamp ? date('d/m/Y', $timestamp) : '-';
}

function riwayatStatus($transaction)
{
    $status = strtolower(trim($transaction['status'] ?? ''));

    if (!empty($transaction['returnedAt']) ||
        in_array($status, ['returned', 'dikembalikan'])) {
        return [
            'label' => 'Dikembalikan',
            'class' => 'history-returned'
        ];
    }

    if (in_array($status, ['overdue', 'terlambat'])) {
        return [
            'label' => 'Terlambat',
            'class' => 'history-overdue'
        ];
    }

    if (in_array($status, ['borrowed', 'dipinjam', 'active', 'aktif'])) {
        return [
            'label' => 'Sedang Dipinjam',
            'class' => 'history-active'
        ];
    }

    return [
        'label' => $transaction['status'] ?? 'Tidak diketahui',
        'class' => 'history-unknown'
    ];
}
?>

<section class="page-header">
    <div>
        <h1>Riwayat Peminjaman</h1>
        <p>Lihat seluruh aktivitas peminjaman e-book kamu.</p>
    </div>
</section>

<section class="history-summary">
    <div class="history-summary-card">
        <div class="history-summary-icon">📖</div>
        <div>
            <p>Total Transaksi</p>
            <h2><?= count($myHistory) ?></h2>
        </div>
    </div>

    <div class="history-summary-card">
        <div class="history-summary-icon">✓</div>
        <div>
            <p>Sudah Dikembalikan</p>
            <h2>
                <?php
                $returnedCount = 0;

                foreach ($myHistory as $transaction) {
                    $status = riwayatStatus($transaction);

                    if ($status['class'] === 'history-returned') {
                        $returnedCount++;
                    }
                }

                echo $returnedCount;
                ?>
            </h2>
        </div>
    </div>
</section>

<section class="content-card history-content">
    <div class="content-card-header">
        <div>
            <h2>Semua Riwayat</h2>
            <p>Daftar transaksi peminjaman e-book kamu.</p>
        </div>
    </div>

    <?php if ($currentUserId === null): ?>

        <div class="empty-state">
            <div class="empty-state-icon">♙</div>
            <h3>Identitas pengguna belum tersedia</h3>
            <p>Silakan login terlebih dahulu untuk melihat riwayat.</p>
        </div>

    <?php elseif (empty($myHistory)): ?>

        <div class="empty-state">
            <div class="empty-state-icon">📚</div>
            <h3>Belum Ada Riwayat</h3>
            <p>Aktivitas peminjaman e-book kamu akan muncul di sini.</p>
            <a href="index.php?page=katalog" class="btn-primary">
                Jelajahi Katalog
            </a>
        </div>

    <?php else: ?>

        <div class="table-wrapper">
            <table class="history-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Judul E-Book</th>
                        <th>Tanggal Pinjam</th>
                        <th>Jatuh Tempo</th>
                        <th>Tanggal Kembali</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($myHistory as $index => $transaction): ?>
                        <?php
                        $ebookId = (string) ($transaction['ebookId'] ?? '');
                        $ebook = $ebookMap[$ebookId] ?? null;
                        $status = riwayatStatus($transaction);
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
                                <?= riwayatDate($transaction['borrowedAt'] ?? null) ?>
                            </td>

                            <td>
                                <?= riwayatDate($transaction['dueAt'] ?? null) ?>
                            </td>

                            <td>
                                <?= riwayatDate($transaction['returnedAt'] ?? null) ?>
                            </td>

                            <td>
                                <span class="status-badge <?= $status['class'] ?>">
                                    <?= htmlspecialchars($status['label']) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php endif; ?>
</section>
