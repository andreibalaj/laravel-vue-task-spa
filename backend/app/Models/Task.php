<?php

namespace App\Models;

class Task
{

    public function __construct(
        public string $id,
        public string $userId,
        public string $title,
        public bool $completed,
        public \DateTimeInterface $createdAt
    ) {}

    public static function fromFirestore(array $data, string $id): self
    {
        return new self(
            id: $id,
            userId: $data['userId'],
            title: $data['title'],
            completed: $data['completed'] ?? false,
            createdAt: new \DateTimeImmutable($data['createdAt'] ?? 'now')
        );
    }

    public function toFirestore(): array
    {
        return [
            'userId' => $this->userId,
            'title' => $this->title,
            'completed' => $this->completed,
            'createdAt' => $this->createdAt->format(\Datetime::RFC3339)
        ];
    }
}
