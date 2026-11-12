<?php

namespace App\Models;

class Ebook extends Media
{
    private string $author;
    private string $publisher;
    private string $category;
    private string $isbn;
    private int $publishedYear;
    private int $pages;
    private string $language;
    private string $format;
    private string $description;
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
        parent::__construct($id, $title);
        $this->author          = $author;
        $this->publisher       = $publisher;
        $this->category        = $category;
        $this->isbn            = $isbn;
        $this->publishedYear   = $publishedYear;
        $this->pages           = $pages;
        $this->language        = $language;
        $this->format          = $format;
        $this->description     = $description;
        $this->totalCopies     = max(0, $totalCopies);
        $this->availableCopies = max(0, $availableCopies);
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function getPublisher(): string
    {
        return $this->publisher;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function getIsbn(): string
    {
        return $this->isbn;
    }

    public function getPublishedYear(): int
    {
        return $this->publishedYear;
    }

    public function getPages(): int
    {
        return $this->pages;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getTotalCopies(): int
    {
        return $this->totalCopies;
    }

    public function getAvailableCopies(): int
    {
        return $this->availableCopies;
    }

    public function isAvailable(): bool
    {
        return $this->availableCopies > 0;
    }

    public function decrementAvailableCopies(): void
    {
        if ($this->availableCopies > 0) {
            $this->availableCopies--;
        }
    }

    public function incrementAvailableCopies(): void
    {
        if ($this->availableCopies < $this->totalCopies) {
            $this->availableCopies++;
        }
    }

    public static function fromArray(array $data): self
    {
        $total = max(0, (int) ($data['totalCopies'] ?? 1));
        $available = isset($data['availableCopies'])
            ? max(0, (int) $data['availableCopies'])
            : $total;

        return new self(
            id: (string) ($data['id'] ?? ''),
            title: (string) ($data['title'] ?? ''),
            author: (string) ($data['author'] ?? ''),
            publisher: (string) ($data['publisher'] ?? ''),
            category: (string) ($data['category'] ?? ''),
            isbn: (string) ($data['isbn'] ?? ''),
            publishedYear: (int) ($data['publishedYear'] ?? date('Y')),
            pages: (int) ($data['pages'] ?? 0),
            language: (string) ($data['language'] ?? 'Indonesia'),
            format: (string) ($data['format'] ?? 'PDF'),
            description: (string) ($data['description'] ?? ''),
            totalCopies: $total,
            availableCopies: $available
        );
    }

    public function toArray(): array
    {
        return [
            'id'              => $this->getId(),
            'title'           => $this->getTitle(),
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
