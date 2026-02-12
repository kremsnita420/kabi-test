<?php

declare(strict_types=1);

namespace App\Models;

final class ProductRepository
{
    private array $products = [
        1 => ['id' => 1, 'name' => 'Product One', 'price' => 29.99, 'description' => 'First product'],
        2 => ['id' => 2, 'name' => 'Product Two', 'price' => 39.99, 'description' => 'Second product'],
        3 => ['id' => 3, 'name' => 'Product Three', 'price' => 49.99, 'description' => 'Third product'],
        4 => ['id' => 4, 'name' => 'Product Four', 'price' => 59.99, 'description' => 'Fourth product'],
        5 => ['id' => 5, 'name' => 'Product Five', 'price' => 69.99, 'description' => 'Fifth product'],
    ];

    public function all(): array
    {
        return array_values($this->products);
    }

    public function find(int $id): ?array
    {
        return $this->products[$id] ?? null;
    }
}
