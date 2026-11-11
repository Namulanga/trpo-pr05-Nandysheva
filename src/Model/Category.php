<?php
declare(strict_types=1);

final class Category
{
    public function __construct(
        private int $id,
        private string $name
    ) {
        if ($id <= 0) {
            throw new InvalidArgumentException('id категории должен быть положительным числом');
        }
        if (trim($name) === '') {
            throw new InvalidArgumentException('Название категории не может быть пустым');
        }
    }

    public function id(): int { return $this->id; }
    public function name(): string { return $this->name; }
}