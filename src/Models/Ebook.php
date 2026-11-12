<?php

declare(strict_types=1);

namespace App\Models;

class Ebook extends Media
{
    private string $isbn;
    private int $pages;
    private string $format;
    private int $totalCopies;
    private int $availableCopies;

    public function __construct(
        string $id,
        string $title,
        string $author,
        string $publisher,
        string $category,
        string $isbn,
        int $publishedYear,
        int $pages,
        string $language,
        string $format,
        string $description,
        int $totalCopies,
        int $availableCopies
    ) {
        parent::__construct(
            $id,
            $title,
            $author,
            $publisher,
            $category,
            $publishedYear,
            $language,
            $description
        );

        $this->setIsbn($isbn);
        $this->setPages($pages);
        $this->setFormat($format);
        $this->setTotalCopies($totalCopies);
        $this->setAvailableCopies($availableCopies);
    }

    public function getIsbn(): string
    {
        return $this->isbn;
    }

    public function setIsbn(string $isbn): void
    {
        $this->isbn = trim($isbn);
    }

    public function getPages(): int
    {
        return $this->pages;
    }

    public function setPages(int $pages): void
    {
        if ($pages < 1) {
            throw new \InvalidArgumentException('Jumlah halaman minimal 1.');
        }
        $this->pages = $pages;
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    public function setFormat(string $format): void
    {
        $upperFormat = strtoupper(trim($format));
        $allowed = ['PDF', 'EPUB'];
        if (!in_array($upperFormat, $allowed, true)) {
            throw new \InvalidArgumentException("Format e-book harus PDF atau EPUB, diberikan: {$format}");
        }
        $this->format = $upperFormat;
    }

    public function getTotalCopies(): int
    {
        return $this->totalCopies;
    }

    public function setTotalCopies(int $totalCopies): void
    {
        if ($totalCopies < 0) {
            throw new \InvalidArgumentException('Total salinan tidak boleh negatif.');
        }
        $this->totalCopies = $totalCopies;
    }

    public function getAvailableCopies(): int
    {
        return $this->availableCopies;
    }

    public function setAvailableCopies(int $availableCopies): void
    {
        if ($availableCopies < 0 || $availableCopies > $this->totalCopies) {
            throw new \InvalidArgumentException('Jumlah salinan tersedia tidak valid.');
        }
        $this->availableCopies = $availableCopies;
    }

    public function isAvailable(): bool
    {
        return $this->availableCopies > 0;
    }

    public function borrowCopy(): void
    {
        if (!$this->isAvailable()) {
            throw new \RuntimeException("Salinan e-book '{$this->title}' sedang habis.");
        }
        $this->availableCopies--;
    }

    public function returnCopy(): void
    {
        if ($this->availableCopies < $this->totalCopies) {
            $this->availableCopies++;
        }
    }

    // Implementasi Polymorphism
    public function getMaxLoanDays(string $userRole = 'member'): int
    {
        if ($userRole === 'admin') {
            return 30;
        }

        return match ($this->format) {
            'EPUB'  => 14,
            default => 7,
        };
    }

    public function getSummary(): string
    {
        return "{$this->title} ({$this->format}, {$this->pages} hlm) oleh {$this->author} — Tersedia: {$this->availableCopies}/{$this->totalCopies}";
    }

    public static function fromArray(array $data): self
    {
        return new self(
            (string) ($data['id'] ?? ''),
            (string) ($data['title'] ?? ''),
            (string) ($data['author'] ?? ''),
            (string) ($data['publisher'] ?? ''),
            (string) ($data['category'] ?? ''),
            (string) ($data['isbn'] ?? ''),
            (int) ($data['publishedYear'] ?? date('Y')),
            (int) ($data['pages'] ?? 1),
            (string) ($data['language'] ?? 'Indonesia'),
            (string) ($data['format'] ?? 'PDF'),
            (string) ($data['description'] ?? ''),
            (int) ($data['totalCopies'] ?? 1),
            (int) ($data['availableCopies'] ?? 1)
        );
    }

    public function toArray(): array
    {
        return [
            'id'              => $this->id,
            'title'           => $this->title,
            'author'          => $this->author,
            'publisher'       => $this->publisher,
            'category'        => $this->category,
            'isbn'            => $this->isbn,
            'publishedYear'   => $this->publishedYear,
            'pages'           => $this->pages,
            'language'        => $this->language,
            'format'          => $this->format,
            'description'     => $this->description,
            'totalCopies'     => $this->totalCopies,
            'availableCopies' => $this->availableCopies,
        ];
    }
}