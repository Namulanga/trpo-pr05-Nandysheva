<?php
declare(strict_types=1);

final class Product
{
    public function __construct(
        private int $id,
        private string $name,
        private float $price,
        private int $categoryId,
        private bool $active = true
    ) {
        if ($id <= 0) {
            throw new InvalidArgumentException('id товара должен быть положительным числом');
        }
        if (trim($name) === '') {
            throw new InvalidArgumentException('Название товара не может быть пустым');
        }
        if ($price < 0) {
            throw new InvalidArgumentException('Цена товара не может быть отрицательной');
        }
        if ($categoryId <= 0) {
            throw new InvalidArgumentException('categoryId должен быть положительным числом');
        }
    }
    public function id(): int { return $this->id; }
    public function name(): string { return $this->name; }
    public function price(): float { return $this->price; }
    public function categoryId(): int { return $this->categoryId; }
}