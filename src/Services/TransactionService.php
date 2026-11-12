<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;

class TransactionService
{
    public function __construct(
        private CatalogService $catalogService,
        private string $transactionLogPath
    ) {
        $this->ensureLogExists();
    }

    private function ensureLogExists(): void
    {
        $dir = dirname($this->transactionLogPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        if (!file_exists($this->transactionLogPath)) {
            file_put_contents($this->transactionLogPath, json_encode([], JSON_PRETTY_PRINT));
        }
    }

    public function prosesPenyewaan(User $user, string $ebookId, int $durasiHari): array
    {
        if ($durasiHari < 1 || $durasiHari > 30) {
            throw new \InvalidArgumentException('Durasi peminjaman harus antara 1 hingga 30 hari.');
        }

        $media = $this->catalogService->findById($ebookId);
        if ($media === null) {
            throw new \InvalidArgumentException('E-Book yang dipilih tidak ditemukan dalam katalog.');
        }

        if (!$media->isAvailable()) {
            throw new \RuntimeException("Maaf, stok salinan untuk '{$media->getTitle()}' sedang habis (0 tersedia).");
        }

        // Pemanggilan metode polimorfik pada objek Media/Ebook
        $subtotal = $media->hitungBiayaSewa($durasiHari);
        $biayaLisensi = $media->getBiayaLisensi();
        $diskon = $user->hitungDiskon($subtotal);
        $totalBayar = $subtotal - $diskon;

        // Kurangi availableCopies sebanyak 1 dan simpan ke ebooks.json
        if (!$this->catalogService->borrowCopy($ebookId)) {
            throw new \RuntimeException('Gagal memperbarui ketersediaan salinan e-book.');
        }

        $transaksi = [
            'kode_transaksi'  => 'TRX-' . strtoupper(bin2hex(random_bytes(3))),
            'waktu'           => date('d M Y, H:i') . ' WIB',
            'member_id'       => $user->getMemberId(),
            'nama'            => $user->getNama(),
            'email'           => $user->getEmail(),
            'tipe_member'     => strtoupper($user->getTipeMember()),
            'ebook_id'        => $media->getId(),
            'title'           => $media->getTitle(),
            'author'          => $media->getAuthor(),
            'publisher'       => $media->getPublisher(),
            'category'        => $media->getCategory(),
            'isbn'            => $media->getIsbn(),
            'format'          => $media->getFormat(),
            'availableCopies' => $media->getAvailableCopies(),
            'totalCopies'     => $media->getTotalCopies(),
            'durasi'          => $durasiHari . ' Hari',
            'biaya_lisensi'   => $biayaLisensi,
            'diskon'          => $diskon,
            'total_angka'     => $totalBayar,
            'total_bayar'     => 'Rp ' . number_format($totalBayar, 0, ',', '.'),
        ];

        $this->appendTransaction($transaksi);

        return $transaksi;
    }

    public function getAllTransactions(): array
    {
        $raw = file_get_contents($this->transactionLogPath);
        $data = json_decode($raw ?: '[]', true);
        return is_array($data) ? $data : [];
    }

    private function appendTransaction(array $transaksi): void
    {
        $history = $this->getAllTransactions();
        array_unshift($history, $transaksi);

        file_put_contents(
            $this->transactionLogPath,
            json_encode($history, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }

    public function clearTransactions(): void
    {
        file_put_contents($this->transactionLogPath, json_encode([], JSON_PRETTY_PRINT));
    }
}
