<?php

declare(strict_types=1);

namespace App\Models;

abstract class Media
{
    public function __construct(
        protected string $id,
        protected string $title,
        protected string $author,
        protected string $publisher,
        protected string $category,
        protected int $publishedYear,
        protected string $language,
        protected string $description
    ) {
        $this->setTitle($title);
        $this->setPublishedYear($publishedYear);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $cleanTitle = trim($title);
        if ($cleanTitle === '') {
            throw new \InvalidArgumentException('Judul media tidak boleh kosong.');
        }
        $this->title = $cleanTitle;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function setAuthor(string $author): void
    {
        $this->author = trim($author);
    }

    public function getPublisher(): string
    {
        return $this->publisher;
    }

    public function setPublisher(string $publisher): void
    {
        $this->publisher = trim($publisher);
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function setCategory(string $category): void
    {
        $this->category = trim($category);
    }

    public function getPublishedYear(): int
    {
        return $this->publishedYear;
    }

    public function setPublishedYear(int $publishedYear): void
    {
        if ($publishedYear < 1000 || $publishedYear > (int) date('Y') + 1) {
            throw new \InvalidArgumentException("Tahun terbit tidak valid: {$publishedYear}");
        }
        $this->publishedYear = $publishedYear;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function setLanguage(string $language): void
    {
        $this->language = trim($language);
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = trim($description);
    }

    // Metode polimorfik untuk di-override pada class turunan
    abstract public function getMaxLoanDays(string $userRole = 'member'): int;

    abstract public function getSummary(): string;

    abstract public function toArray(): array;
}