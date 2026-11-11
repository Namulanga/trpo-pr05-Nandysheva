<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/Model/Product.php';
require_once __DIR__ . '/../src/Model/Category.php';
require_once __DIR__ . '/../src/Service/CatalogService.php';

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

$service = new CatalogService(
    __DIR__ . '/../data/products.json',
    __DIR__ . '/../data/categories.json'
);

$products = [];
$categories = [];
$fatalError = null;

try {
    $products = $service->loadProducts();
    $categories = $service->loadCategories();
} catch (RuntimeException $e) {
    $fatalError = $e->getMessage();
}

$selectedId = null;
$filterMessage = null;
$raw = $_GET['category'] ?? '';

if ($raw !== '') {
    $id = filter_var($raw, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if ($id === false || !isset($categories[$id])) {
        $filterMessage = 'Такой категории нет, показаны все товары.';
    } else {
        $selectedId = $id;
    }
}

$visible = $service->filterByCategory($products, $selectedId);
$average = $service->averagePrice($visible);
$availableCount = count(array_filter($visible, fn (Product $p) => $p->isAvailable()));
?>
<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Каталог товаров</title>
  <style>
    *{box-sizing:border-box}
    body{margin:0;background:#f4f6fb;color:#1f2937;font-family:Arial,sans-serif}
    header{background:#111827;color:#fff;padding:18px 32px;font-weight:700;font-size:20px}
    main{max-width:1100px;margin:28px auto;padding:0 20px}
    .stats{display:flex;flex-wrap:wrap;gap:12px;margin:16px 0}
    .stat{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:12px 18px}
    .stat b{display:block;font-size:22px}
    form{display:flex;flex-wrap:wrap;gap:10px;align-items:center}
    select,button{padding:8px 12px;font-size:16px}
    .grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(230px,1fr));gap:16px;margin-top:16px}
    .card{background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:16px}
    .card h3{margin:0 0 6px}
    .muted{color:#6b7280;font-size:14px}
    .price{font-size:20px;font-weight:700;margin:10px 0 2px}
    .badge{display:inline-block;border-radius:999px;padding:3px 10px;font-size:13px}
    .ok{background:#dcfce7;color:#166534}.no{background:#fee2e2;color:#991b1b}
    .notice{background:#fff7ed;border:1px solid #fed7aa;border-radius:10px;padding:10px 14px;margin:12px 0}
    .error{background:#fee2e2;border:1px solid #fecaca;border-radius:10px;padding:10px 14px;margin:12px 0}
  </style>
</head>
<body>
<header>Каталог товаров</header>
<main>
  <?php if ($fatalError !== null): ?>
    <div class="error" role="alert">Ошибка данных: <?= e($fatalError) ?></div>
  <?php else: ?>

    <form method="get" action="">
      <label for="category">Категория:</label>
      <select name="category" id="category">
        <option value="">Все категории</option>
        <?php foreach ($categories as $category): ?>
          <option value="<?= $category->id() ?>"<?= $selectedId === $category->id() ? ' selected' : '' ?>>
            <?= e($category->name()) ?>
          </option>
        <?php endforeach; ?>
      </select>
      <button type="submit">Показать</button>
      <a href="?">Сбросить</a>
    </form>

    <?php if ($filterMessage !== null): ?>
      <div class="notice"><?= e($filterMessage) ?></div>
    <?php endif; ?>

    <?php foreach ($service->errors() as $message): ?>
      <div class="notice"><?= e($message) ?></div>
    <?php endforeach; ?>

    <div class="stats">
      <div class="stat"><b><?= count($visible) ?></b>товаров</div>
      <div class="stat"><b><?= $availableCount ?></b>в наличии</div>
      <div class="stat"><b><?= e(number_format($average, 2, ',', ' ')) ?> ₽</b>средняя цена</div>
    </div>

    <?php if (count($visible) === 0): ?>
      <p class="muted">В этой категории пока нет товаров.</p>
    <?php else: ?>
      <div class="grid">
        <?php foreach ($visible as $product): ?>
          <?php $category = $categories[$product->categoryId()] ?? null; ?>
          <article class="card">
            <h3><?= e($product->name()) ?></h3>
            <div class="muted"><?= e($category !== null ? $category->name() : 'Без категории') ?></div>
            <div class="price"><?= e($product->formattedPrice()) ?></div>
            <div class="muted">со скидкой 10%: <?= e($product->formattedPrice($product->priceWithDiscount(10))) ?></div>
            <p>
              <?php if ($product->isAvailable()): ?>
                <span class="badge ok">в наличии</span>
              <?php else: ?>
                <span class="badge no">нет в наличии</span>
              <?php endif; ?>
            </p>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

  <?php endif; ?>
</main>
</body>
</html>