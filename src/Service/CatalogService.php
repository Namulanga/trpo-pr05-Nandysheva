<?php
declare(strict_types=1);

final class CatalogService
{
    /** @var string[]*/
    private array $errors = [];

    public function __construct(
        private string $productsPath,
        private string $categoriesPath
    ) {}

    /** @return Product[] */
    public function loadProducts(): array
    {
        $this->errors = [];
        $products = [];

        foreach ($this->readJson($this->productsPath) as $index => $row) {
            try {
                $products[] = new Product(
                    $row['id'] ?? 0,
                    $row['name'] ?? '',
                    $row['price'] ?? -1,
                    $row['category_id'] ?? 0,
                    $row['active'] ?? true
                );
            } catch (InvalidArgumentException $e) {
                $this->errors[] = 'Товар в строке ' . ($index + 1) . ' пропущен: ' . $e->getMessage();
            } catch (TypeError $e) {
                $this->errors[] = 'Товар в строке ' . ($index + 1) . ' пропущен: у поля неверный тип данных';
            }
        }

        return $products;
    }

    /** @return array<int, Category>*/
    public function loadCategories(): array
    {
        $categories = [];

        foreach ($this->readJson($this->categoriesPath) as $row) {
            try {
                $category = new Category($row['id'] ?? 0, $row['name'] ?? '');
                $categories[$category->id()] = $category;
            } catch (InvalidArgumentException $e) {
                $this->errors[] = 'Категория пропущена: ' . $e->getMessage();
            } catch (TypeError $e) {
                $this->errors[] = 'Категория пропущена: у поля неверный тип данных';
            }
        }

        return $categories;
    }

    /** @return string[] */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * @param Product[] $products
     * @return Product[]
     */
    public function filterByCategory(array $products, ?int $categoryId): array
    {
        if ($categoryId === null) {
            return $products;
        }
        return array_values(array_filter(
            $products,
            fn (Product $product) => $product->belongsTo($categoryId)
        ));
    }

    /** @param Product[] $products */
    public function averagePrice(array $products): float
    {
        if (count($products) === 0) {
            return 0.0;
        }
        $sum = array_reduce($products, fn (float $carry, Product $p) => $carry + $p->price(), 0.0);
        return $sum / count($products);
    }

    /** @return array<int, mixed> */
    private function readJson(string $path): array
    {
        if (!is_file($path)) {
            throw new RuntimeException('Файл данных не найден: ' . basename($path));
        }

        $text = file_get_contents($path);
        if ($text === false) {
            throw new RuntimeException('Не удалось прочитать файл: ' . basename($path));
        }

        try {
            $data = json_decode($text, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new RuntimeException('Файл ' . basename($path) . ' содержит некорректный JSON');
        }

        if (!is_array($data)) {
            throw new RuntimeException('Файл ' . basename($path) . ' должен содержать список');
        }

        return $data;
    }
}