# Каталог товаров — практическая работа №5
(1–2 предложения: что это за приложение и какие данные оно показывает.)
## Требования
- PHP 8.0 или новее (проверьте: `php -v`)
- Никаких библиотек и Composer не нужно
## Запуск
    git clone <ссылка-на-репозиторий>
    cd trpo-pr05-student
    php -S localhost:8000 -t public
Открыть http://localhost:8000
## Схема
    products.json ──► CatalogService ──► Product[] ──► index.php ──► HTML
    categories.json ─► CatalogService ──► Category[] ──┘

- **Product** — (что хранит и что умеет; без HTML)
- **Category** — (что хранит)
- **CatalogService** — (читает JSON, создаёт объекты, фильтрует, считает среднюю цену)
- **index.php** — (вход, фильтр из адресной строки, экранирование и HTML)
## Как проверить
    php tests/constructor_check.php

