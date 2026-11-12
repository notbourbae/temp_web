<?php

declare(strict_types=1);

require_once __DIR__ . '/src/autoload.php';

use App\Models\User;
use App\Services\CatalogService;
use App\Services\TransactionService;

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Metode HTTP tidak diizinkan. Gunakan POST.',
    ]);
    exit;
}

try {
    $catalogService = new CatalogService(__DIR__ . '/data/ebooks.json');
    $transactionService = new TransactionService($catalogService, __DIR__ . '/data/transactions.json');

    $action = $_POST['action'] ?? 'rent';

    if ($action === 'reset') {
        $catalogService->resetDefaultCatalog();
        $transactionService->clearTransactions();

        echo json_encode([
            'status'  => 'success',
            'message' => 'Data katalog dan riwayat transaksi berhasil direset ke kondisi awal.',
        ]);
        exit;
    }

    $nama       = (string) ($_POST['nama'] ?? '');
    $memberId   = (string) ($_POST['member_id'] ?? '');
    $email      = (string) ($_POST['email'] ?? '');
    $tipeMember = (string) ($_POST['tipe_member'] ?? 'reguler');
    $ebookId    = (string) ($_POST['ebook_id'] ?? '');
    $durasi     = (int) ($_POST['durasi'] ?? 0);

    $user = new User($memberId, $nama, $email, $tipeMember);
    $hasilTransaksi = $transactionService->prosesPenyewaan($user, $ebookId, $durasi);

    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'data'   => $hasilTransaksi,
    ]);
} catch (\InvalidArgumentException | \RuntimeException $e) {
    http_response_code(422);
    echo json_encode([
        'status'  => 'error',
        'message' => $e->getMessage(),
    ]);
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Terjadi kesalahan internal pada server: ' . $e->getMessage(),
    ]);
}
