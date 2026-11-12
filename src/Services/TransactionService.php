<?php

namespace App\Services;

use App\Models\Transaction;

class TransactionService
{
    private string $transactionsFile;
    private EbookService $ebookService;
    private MemberService $memberService;

    public function __construct(
        ?string $transactionsFile = null,
        ?EbookService $ebookService = null,
        ?MemberService $memberService = null
    ) {
        $this->transactionsFile = $transactionsFile ?? __DIR__ . '/../../data/transactions.json';
        $this->ebookService     = $ebookService ?? new EbookService();
        $this->memberService    = $memberService ?? new MemberService();
    }

    public function getAll(): array
    {
        return JsonStorage::read($this->transactionsFile);
    }

    public function getByUserId(string $userId): array
    {
        return array_values(array_filter(
            $this->getAll(),
            fn($t) => ($t['userId'] ?? '') === $userId
        ));
    }

    public function borrowEbook(string $userId, string $ebookId, int $durationDays = 7): array
    {
        $user = $this->memberService->getById($userId);
        if (!$user) {
            return ['success' => false, 'message' => 'Akun pengguna tidak ditemukan.'];
        }

        $ebook = $this->ebookService->getById($ebookId);
        if (!$ebook) {
            return ['success' => false, 'message' => 'E-Book tidak ditemukan.'];
        }

        if ((int) ($ebook['availableCopies'] ?? 0) <= 0) {
            return ['success' => false, 'message' => 'Stok E-Book sedang habis dan tidak tersedia untuk dipinjam.'];
        }

        $transactions = $this->getAll();
        foreach ($transactions as $trx) {
            if (($trx['userId'] ?? '') === $userId && ($trx['ebookId'] ?? '') === $ebookId && ($trx['status'] ?? '') === 'borrowed') {
                return ['success' => false, 'message' => 'Anda masih meminjam E-Book ini. Kembalikan terlebih dahulu sebelum meminjam ulang.'];
            }
        }

        $borrowedAt = date('Y-m-d');
        $dueAt      = date('Y-m-d', strtotime("+{$durationDays} days"));

        $newTrx = new Transaction([
            'id'         => JsonStorage::generateId('TRX', $transactions),
            'userId'     => $userId,
            'ebookId'    => $ebookId,
            'borrowedAt' => $borrowedAt,
            'dueAt'      => $dueAt,
            'returnedAt' => null,
            'status'     => 'borrowed',
        ]);

        if (!$this->ebookService->updateAvailableCopies($ebookId, -1)) {
            return ['success' => false, 'message' => 'Gagal memperbarui stok E-Book.'];
        }

        $transactions[] = $newTrx->toArray();
        JsonStorage::write($this->transactionsFile, $transactions);

        return ['success' => true, 'message' => 'Peminjaman berhasil dicatat. Jatuh tempo: ' . $dueAt, 'data' => $newTrx->toArray()];
    }

    public function returnEbook(string $transactionId, ?string $userId = null): array
    {
        $transactions = $this->getAll();
        $found = false;
        $ebookId = '';

        foreach ($transactions as &$trx) {
            if (($trx['id'] ?? '') === $transactionId) {
                if ($userId !== null && ($trx['userId'] ?? '') !== $userId) {
                    return ['success' => false, 'message' => 'Anda tidak memiliki akses untuk transaksi ini.'];
                }

                if (($trx['status'] ?? '') === 'returned') {
                    return ['success' => false, 'message' => 'E-Book pada transaksi ini sudah dikembalikan sebelumnya.'];
                }

                $trx['returnedAt'] = date('Y-m-d');
                $trx['status']     = 'returned';
                $ebookId           = (string) ($trx['ebookId'] ?? '');
                $found             = true;
                break;
            }
        }
        unset($trx);

        if (!$found) {
            return ['success' => false, 'message' => 'Transaksi peminjaman tidak ditemukan.'];
        }

        JsonStorage::write($this->transactionsFile, $transactions);
        if ($ebookId !== '') {
            $this->ebookService->updateAvailableCopies($ebookId, 1);
        }

        return ['success' => true, 'message' => 'E-Book berhasil dikembalikan dan stok telah diperbarui.'];
    }

    public function getDashboardStats(): array
    {
        $ebooks       = $this->ebookService->getAll();
        $members      = $this->memberService->getAllMembers();
        $transactions = $this->getAll();

        $ebookMap = [];
        foreach ($ebooks as $eb) {
            $ebookMap[$eb['id']] = $eb['title'];
        }

        $memberMap = [];
        foreach ($members as $mb) {
            $memberMap[$mb['id']] = $mb['name'];
        }

        $activeLoans   = 0;
        $returnedLoans = 0;
        foreach ($transactions as $trx) {
            if (($trx['status'] ?? '') === 'borrowed') {
                $activeLoans++;
            } elseif (($trx['status'] ?? '') === 'returned') {
                $returnedLoans++;
            }
        }

        $recent = array_slice(array_reverse($transactions), 0, 5);
        foreach ($recent as &$r) {
            $r['ebookTitle'] = $ebookMap[$r['ebookId']] ?? $r['ebookId'];
            $r['userName']   = $memberMap[$r['userId']] ?? $r['userId'];
        }
        unset($r);

        return [
            'totalEbooks'        => count($ebooks),
            'totalMembers'       => count($members),
            'totalTransactions'  => count($transactions),
            'activeLoans'        => $activeLoans,
            'returnedLoans'      => $returnedLoans,
            'recentTransactions' => $recent,
        ];
    }
}
