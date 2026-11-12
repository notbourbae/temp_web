<?php

namespace App\Models;

class Transaction
{
    public string $id;
    public string $userId;
    public string $ebookId;
    public string $borrowedAt;
    public string $dueAt;
    public ?string $returnedAt;
    public string $status;

    public function __construct(array $data)
    {
        $this->id         = (string) ($data['id'] ?? '');
        $this->userId     = (string) ($data['userId'] ?? '');
        $this->ebookId    = (string) ($data['ebookId'] ?? '');
        $this->borrowedAt = (string) ($data['borrowedAt'] ?? date('Y-m-d'));
        $this->dueAt      = (string) ($data['dueAt'] ?? date('Y-m-d', strtotime('+7 days')));
        $this->returnedAt = isset($data['returnedAt']) && $data['returnedAt'] !== ''
            ? (string) $data['returnedAt']
            : null;
        $this->status     = (string) ($data['status'] ?? 'borrowed');
    }

    public static function fromArray(array $data): self
    {
        return new self($data);
    }

    public function toArray(): array
    {
        return [
            'id'         => $this->id,
            'userId'     => $this->userId,
            'ebookId'    => $this->ebookId,
            'borrowedAt' => $this->borrowedAt,
            'dueAt'      => $this->dueAt,
            'returnedAt' => $this->returnedAt,
            'status'     => $this->status,
        ];
    }
}
