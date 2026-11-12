<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Ebook;
use App\Models\Media;

class CatalogService
{
    /** @var Media[] */
    private array $items = [];

    public function __construct(
        private string $storagePath
    ) {
        $this->ensureStorageExists();
        $this->loadCatalog();
    }

    private function ensureStorageExists(): void
    {
        $dir = dirname($this->storagePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        if (!file_exists($this->storagePath) || filesize($this->storagePath) === 0) {
            $this->resetDefaultCatalog();
        }
    }

    private function loadCatalog(): void
    {
        $rawJson = file_get_contents($this->storagePath);
        $decoded = json_decode($rawJson ?: '[]', true);

        if (!is_array($decoded)) {
            $decoded = [];
        }

        $this->items = [];
        foreach ($decoded as $row) {
            $this->items[] = $this->hydrateMedia($row);
        }
    }

    private function hydrateMedia(array $data): Media
    {
        return new Ebook(
            id: (string) $data['id'],
            title: (string) $data['title'],
            author: (string) $data['author'],
            publisher: (string) $data['publisher'],
            category: (string) $data['category'],
            isbn: (string) $data['isbn'],
            publishedYear: (int) $data['publishedYear'],
            pages: (int) $data['pages'],
            language: (string) $data['language'],
            format: (string) $data['format'],
            description: (string) $data['description'],
            totalCopies: (int) $data['totalCopies'],
            availableCopies: (int) $data['availableCopies']
        );
    }

    /**
     * @return Media[]
     */
    public function getAllMedia(): array
    {
        return $this->items;
    }

    public function findById(string $id): ?Media
    {
        foreach ($this->items as $media) {
            if ($media->getId() === $id) {
                return $media;
            }
        }
        return null;
    }

    /**
     * Mengurangi salinan tersedia (availableCopies) saat e-book disewa/dipinjam.
     */
    public function borrowCopy(string $id): bool
    {
        $media = $this->findById($id);
        if ($media === null || !$media->isAvailable()) {
            return false;
        }

        $media->decrementAvailableCopies();
        $this->persist();
        return true;
    }

    /**
     * Menambah kembali salinan tersedia (availableCopies) saat e-book dikembalikan.
     */
    public function returnCopy(string $id): bool
    {
        $media = $this->findById($id);
        if ($media === null) {
            return false;
        }

        $media->incrementAvailableCopies();
        $this->persist();
        return true;
    }

    private function persist(): void
    {
        $serialized = array_map(
            static fn(Media $media): array => $media->toArray(),
            $this->items
        );

        file_put_contents(
            $this->storagePath,
            json_encode($serialized, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }

    public function resetDefaultCatalog(): void
    {
        $defaultData = [
            [
                'id'              => 'EB-001',
                'title'           => 'PHP 8 Objects, Patterns, and Practice',
                'author'          => 'Matt Zandstra',
                'publisher'       => 'Apress',
                'category'        => 'Programming',
                'isbn'            => '978-1484267905',
                'publishedYear'   => 2021,
                'pages'           => 824,
                'language'        => 'English',
                'format'          => 'PDF',
                'description'     => 'Panduan mendalam arsitektur OOP modern, design patterns, dan fitur terbaru PHP 8.',
                'totalCopies'     => 5,
                'availableCopies' => 4,
            ],
            [
                'id'              => 'EB-002',
                'title'           => 'Clean Code: A Handbook of Agile Software Craftsmanship',
                'author'          => 'Robert C. Martin',
                'publisher'       => 'Prentice Hall',
                'category'        => 'Software Engineering',
                'isbn'            => '978-0132350884',
                'publishedYear'   => 2008,
                'pages'           => 464,
                'language'        => 'English',
                'format'          => 'EPUB',
                'description'     => 'Prinsip penulisan kode yang bersih, modular, mudah dibaca, dan terstruktur.',
                'totalCopies'     => 4,
                'availableCopies' => 2,
            ],
            [
                'id'              => 'EB-003',
                'title'           => 'Arsitektur Web Modern dengan HTML, CSS, dan JavaScript',
                'author'          => 'Budi Raharjo',
                'publisher'       => 'Informatika Bandung',
                'category'        => 'Web Development',
                'isbn'            => '978-6237131540',
                'publishedYear'   => 2024,
                'pages'           => 380,
                'language'        => 'Indonesian',
                'format'          => 'PDF',
                'description'     => 'Membangun antarmuka web semantik, responsif, dan interaktif tanpa ketergantungan framework.',
                'totalCopies'     => 6,
                'availableCopies' => 6,
            ],
            [
                'id'              => 'EB-004',
                'title'           => 'Designing Data-Intensive Applications',
                'author'          => 'Martin Kleppmann',
                'publisher'       => "O'Reilly Media",
                'category'        => 'Distributed Systems',
                'isbn'            => '978-1449373320',
                'publishedYear'   => 2017,
                'pages'           => 616,
                'language'        => 'English',
                'format'          => 'EPUB',
                'description'     => 'Konsep skalabilitas aplikasi web, replikasi data, dan konsistensi sistem terdistribusi.',
                'totalCopies'     => 3,
                'availableCopies' => 0,
            ],
        ];

        $this->items = array_map(
            fn(array $row): Media => $this->hydrateMedia($row),
            $defaultData
        );
        $this->persist();
    }
}
