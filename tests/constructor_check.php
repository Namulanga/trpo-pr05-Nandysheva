<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/Model/Product.php';

$cases = [
    'корректный товар'   => [10, 'Клавиатура', 3500.0, 1],
    'пустое название'    => [10, '   ', 3500.0, 1],
    'отрицательная цена' => [10, 'Клавиатура', -5.0, 1],
    'нулевой id'         => [0, 'Клавиатура', 3500.0, 1],
];

foreach ($cases as $title => $args) {
    try {
        $product = new Product(...$args);
        echo "OK     {$title}: {$product->name()}, {$product->formattedPrice()}\n";
    } catch (InvalidArgumentException $e) {
        echo "ОШИБКА {$title}: {$e->getMessage()}\n";
    }
}