<?php

namespace App\Services;

use App\Models\Ebook;

class EbookService
{
    private string $ebooksFile;
    private string $transactionsFile;

    public function __construct(?string $ebooksFile = null, ?string $transactionsFile = null)
    {
        $this->ebooksFile       = $ebooksFile ?? __DIR__ . '/../../data/ebooks.json';
        $this->transactionsFile = $transactionsFile ?? __DIR__ . '/../../data/transactions.json';
    }

    public function getAll(?string $search = null, ?string $category = null): array
    {
        $ebooks = JsonStorage::read($this->ebooksFile);

        if ($category !== null && trim($category) !== '' && strtolower($category) !== 'all') {
            $catLower = strtolower(trim($category));
            $ebooks = array_filter($ebooks, fn($b) => strtolower($b['category'] ?? '') === $catLower);
        }

        if ($search !== null && trim($search) !== '') {
            $kw = strtolower(trim($search));
            $ebooks = array_filter($ebooks, function ($b) use ($kw) {
                return str_contains(strtolower($b['title'] ?? ''), $kw)
                    || str_contains(strtolower($b['author'] ?? ''), $kw)
                    || str_contains(strtolower($b['publisher'] ?? ''), $kw)
                    || str_contains(strtolower($b['isbn'] ?? ''), $kw);
            });
        }

        return array_values($ebooks);
    }

    public function getById(string $id): ?array
    {
        foreach (JsonStorage::read($this->ebooksFile) as $ebook) {
            if (($ebook['id'] ?? '') === $id) {
                return $ebook;
            }
        }
        return null;
    }

    public function create(array $data): array
    {
        $title    = trim($data['title'] ?? '');
        $author   = trim($data['author'] ?? '');
        $category = trim($data['category'] ?? '');

        if ($title === '' || $author === '' || $category === '') {
            return ['success' => false, 'message' => 'Judul, penulis, dan kategori E-Book wajib diisi.'];
        }

        $totalCopies = max(1, (int) ($data['totalCopies'] ?? 1));
        $availableCopies = isset($data['availableCopies']) && $data['availableCopies'] !== ''
            ? min($totalCopies, max(0, (int) $data['availableCopies']))
            : $totalCopies;

        $ebooks = JsonStorage::read($this->ebooksFile);
        $data['id']              = JsonStorage::generateId('EBK', $ebooks);
        $data['totalCopies']     = $totalCopies;
        $data['availableCopies'] = $availableCopies;

        $ebook = Ebook::fromArray($data);
        $ebooks[] = $ebook->toArray();
        JsonStorage::write($this->ebooksFile, $ebooks);

        return ['success' => true, 'message' => 'E-Book berhasil ditambahkan.', 'data' => $ebook->toArray()];
    }

    public function update(string $id, array $data): array
    {
        $ebooks = JsonStorage::read($this->ebooksFile);
        $found = false;

        foreach ($ebooks as &$item) {
            if (($item['id'] ?? '') === $id) {
                $title  = trim($data['title'] ?? $item['title']);
                $author = trim($data['author'] ?? $item['author']);
                if ($title === '' || $author === '') {
                    return ['success' => false, 'message' => 'Judul dan penulis tidak boleh kosong.'];
                }

                $oldTotal     = (int) ($item['totalCopies'] ?? 1);
                $oldAvailable = (int) ($item['availableCopies'] ?? $oldTotal);
                $borrowedCount = max(0, $oldTotal - $oldAvailable);

                $newTotal = isset($data['totalCopies']) ? max($borrowedCount, (int) $data['totalCopies']) : $oldTotal;
                $newAvailable = isset($data['availableCopies']) && $data['availableCopies'] !== ''
                    ? min($newTotal, max(0, (int) $data['availableCopies']))
                    : max(0, $newTotal - $borrowedCount);

                $merged = array_merge($item, [
                    'title'           => $title,
                    'author'          => $author,
                    'publisher'       => trim($data['publisher'] ?? $item['publisher'] ?? ''),
                    'category'        => trim($data['category'] ?? $item['category'] ?? ''),
                    'isbn'            => trim($data['isbn'] ?? $item['isbn'] ?? ''),
                    'publishedYear'   => (int) ($data['publishedYear'] ?? $item['publishedYear'] ?? date('Y')),
                    'pages'           => (int) ($data['pages'] ?? $item['pages'] ?? 0),
                    'language'        => trim($data['language'] ?? $item['language'] ?? 'Indonesia'),
                    'format'          => trim($data['format'] ?? $item['format'] ?? 'PDF'),
                    'description'     => trim($data['description'] ?? $item['description'] ?? ''),
                    'totalCopies'     => $newTotal,
                    'availableCopies' => $newAvailable,
                ]);

                $item = Ebook::fromArray($merged)->toArray();
                $found = true;
                break;
            }
        }
        unset($item);

        if (!$found) {
            return ['success' => false, 'message' => 'Data E-Book tidak ditemukan.'];
        }

        JsonStorage::write($this->ebooksFile, $ebooks);
        return ['success' => true, 'message' => 'Data E-Book berhasil diperbarui.'];
    }

    public function delete(string $id): array
    {
        $transactions = JsonStorage::read($this->transactionsFile);
        foreach ($transactions as $trx) {
            if (($trx['ebookId'] ?? '') === $id && ($trx['status'] ?? '') === 'borrowed') {
                return ['success' => false, 'message' => 'E-Book tidak dapat dihapus karena masih sedang dipinjam.'];
            }
        }

        $ebooks = JsonStorage::read($this->ebooksFile);
        $filtered = array_filter($ebooks, fn($b) => ($b['id'] ?? '') !== $id);

        if (count($filtered) === count($ebooks)) {
            return ['success' => false, 'message' => 'Data E-Book tidak ditemukan.'];
        }

        JsonStorage::write($this->ebooksFile, $filtered);
        return ['success' => true, 'message' => 'E-Book berhasil dihapus.'];
    }

    public function updateAvailableCopies(string $id, int $delta): bool
    {
        $ebooks = JsonStorage::read($this->ebooksFile);
        foreach ($ebooks as &$item) {
            if (($item['id'] ?? '') === $id) {
                $total = (int) ($item['totalCopies'] ?? 0);
                $nextAvailable = (int) ($item['availableCopies'] ?? 0) + $delta;
                if ($nextAvailable < 0 || $nextAvailable > $total) {
                    return false;
                }
                $item['availableCopies'] = $nextAvailable;
                return JsonStorage::write($this->ebooksFile, $ebooks);
            }
        }
        return false;
    }
}
