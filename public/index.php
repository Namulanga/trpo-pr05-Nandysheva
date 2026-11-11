<?php
require_once __DIR__ . '/../src/Model/Product.php';

$p = new Product(10, 'Keyboard', 3500.0, 1);
echo $p->name() . ' — ' . $p->price();