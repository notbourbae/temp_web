
<?php
function readAdminDashboardJson($filePath)
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

$ebooks = readAdminDashboardJson(__DIR__ . '/../../data/ebooks.json');
$transactions = readAdminDashboardJson(__DIR__ . '/../../data/transactions.json');

// Statistik e-book
$totalBooks = count($ebooks);

$availableBooks = 0;

foreach ($ebooks as $ebook) {
    $availableBooks += (int) ($ebook['availableCopies'] ?? 0);
}

// Statistik transaksi
$totalTransactions = count($transactions);
$activeTransactions = 0;
$returnedTransactions = 0;

foreach ($transactions as $transaction) {
    $status = strtolower(trim($transaction['status'] ?? ''));

    if (
        in_array($status, ['borrowed', 'dipinjam', 'active', 'aktif']) &&
        empty($transaction['returnedAt'])
    ) {
        $activeTransactions++;
    }

    if (
        !empty($transaction['returnedAt']) ||
        in_array($status, ['returned', 'dikembalikan'])
    ) {
        $returnedTransactions++;
    }
}

// Member belum memiliki sumber data JSON
$totalMembers = 0;

// Pemetaan e-book berdasarkan ID
$ebookMap = [];

foreach ($ebooks as $ebook) {
    if (isset($ebook['id'])) {
        $ebookMap[(string) $ebook['id']] = $ebook;
    }
}

// Mengurutkan transaksi terbaru
$recentTransactions = $transactions;

usort($recentTransactions, function ($a, $b) {
    return strtotime($b['borrowedAt'] ?? '') <=> strtotime($a['borrowedAt'] ?? '');
});

$recentTransactions = array_slice($recentTransactions, 0, 5);

function adminDashboardDate($date)
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
        <h1>Dashboard Admin</h1>
        <p>Ringkasan informasi dan aktivitas E-Book Library.</p>
    </div>
</section>

<!-- Statistik -->
<section class="admin-statistics">

    <div class="admin-stat-card">
        <div class="admin-stat-icon orange">📚</div>
        <div>
            <p>Total E-Book</p>
            <h2><?= $totalBooks ?></h2>
        </div>
    </div>

    <div class="admin-stat-card">
        <div class="admin-stat-icon blue">👥</div>
        <div>
            <p>Total Member</p>
            <h2><?= $totalMembers ?></h2>
        </div>
    </div>

    <div class="admin-stat-card">
        <div class="admin-stat-icon purple">📋</div>
        <div>
            <p>Total Transaksi</p>
            <h2><?= $totalTransactions ?></h2>
        </div>
    </div>

    <div class="admin-stat-card">
        <div class="admin-stat-icon green">↗</div>
        <div>
            <p>Peminjaman Aktif</p>
            <h2><?= $activeTransactions ?></h2>
        </div>
    </div>

</section>

<!-- Ringkasan -->
<section class="admin-overview">

    <div class="content-card">
        <div class="content-card-header">
            <div>
                <h2>Ringkasan Perpustakaan</h2>
                <p>Kondisi data perpustakaan saat ini.</p>
            </div>
        </div>

        <div class="admin-overview-list">

            <div class="overview-item">
                <span>Total Koleksi E-Book</span>
                <strong><?= $totalBooks ?></strong>
            </div>

            <div class="overview-item">
                <span>Total Salinan Tersedia</span>
                <strong><?= $availableBooks ?></strong>
            </div>

            <div class="overview-item">
                <span>Peminjaman Aktif</span>
                <strong><?= $activeTransactions ?></strong>
            </div>

            <div class="overview-item">
                <span>E-Book Sudah Dikembalikan</span>
                <strong><?= $returnedTransactions ?></strong>
            </div>

            <div class="overview-item">
                <span>Total Member</span>
                <strong><?= $totalMembers ?></strong>
            </div>

        </div>
    </div>

</section>

<!-- Transaksi Terbaru -->
<section class="content-card admin-recent-content">

    <div class="content-card-header">
        <div>
            <h2>Transaksi Terbaru</h2>
            <p>Lima aktivitas peminjaman terakhir.</p>
        </div>
    </div>

    <?php if (empty($recentTransactions)): ?>

        <div class="empty-state">
            <div class="empty-state-icon">📋</div>
            <h3>Belum Ada Transaksi</h3>
            <p>Aktivitas peminjaman akan muncul di sini.</p>
        </div>

    <?php else: ?>

        <div class="table-wrapper">
            <table class="admin-recent-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>ID Transaksi</th>
                        <th>E-Book</th>
                        <th>Tanggal Pinjam</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($recentTransactions as $index => $transaction): ?>

                        <?php
                        $ebookId = (string) ($transaction['ebookId'] ?? '');
                        $ebook = $ebookMap[$ebookId] ?? null;

                        $status = strtolower(trim($transaction['status'] ?? ''));

                        if (
                            !empty($transaction['returnedAt']) ||
                            in_array($status, ['returned', 'dikembalikan'])
                        ) {
                            $statusLabel = 'Dikembalikan';
                            $statusClass = 'history-returned';
                        } elseif (
                            in_array($status, ['borrowed', 'dipinjam', 'active', 'aktif'])
                        ) {
                            $statusLabel = 'Sedang Dipinjam';
                            $statusClass = 'history-active';
                        } elseif (
                            in_array($status, ['overdue', 'terlambat'])
                        ) {
                            $statusLabel = 'Terlambat';
                            $statusClass = 'history-overdue';
                        } else {
                            $statusLabel = $transaction['status'] ?? 'Tidak diketahui';
                            $statusClass = 'history-unknown';
                        }
                        ?>

                        <tr>
                            <td><?= $index + 1 ?></td>

                            <td>
                                <?= htmlspecialchars(
                                    (string) ($transaction['id'] ?? '-')
                                ) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $ebook['title'] ?? 'E-Book tidak ditemukan'
                                ) ?>
                            </td>

                            <td>
                                <?= adminDashboardDate(
                                    $transaction['borrowedAt'] ?? null
                                ) ?>
                            </td>

                            <td>
                                <span class="status-badge <?= $statusClass ?>">
                                    <?= htmlspecialchars($statusLabel) ?>
                                </span>
                            </td>
                        </tr>

                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php endif; ?>

</section>
