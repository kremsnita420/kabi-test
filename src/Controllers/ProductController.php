<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ProductRepository;

final class ProductController extends Controller
{
    private ProductRepository $repository;

    public function __construct()
    {
        $this->repository = new ProductRepository();
    }

    public function index(): void
    {
        $products = $this->repository->all();

        // Render dedicated products page instead of a partial so layout can provide full page structure
        $this->render('pages/products', [
            'products' => $products,
            'title'    => 'Izdelki – Kabi Test',
            'head'     => '<meta name="description" content="Seznam izdelkov Kabi Test">',
        ]);
    }

    public function show(string $id): void
    {
        if (!ctype_digit($id))
        {
            http_response_code(404);
            return;
        }

        $product = $this->repository->find((int) $id);

        if (!$product)
        {
            http_response_code(404);
            return;
        }



        // Try a few common keys; adjust if your repo uses different field names.
        $name = $product['name'] ?? $product['title'] ?? $product['product_name'] ?? ('Izdelek #' . $id);
        $galleryItems = $product['gallery_items'] ?? [];
        $firstHero    = $galleryItems[0]['hero'] ?? ($product['images']['hero'] ?? []);

        $preloadImage = $firstHero['webp']
            ?? $firstHero['jpg']
            ?? ($product['image'] ?? null);


        $this->render('pages/product-single', [
            'product' => $product,
            'title'   => $name . ' – Kabi Test',
            'head'    => '<meta name="description" content="Podrobnosti izdelka: ' . $this->e((string) $name) . '">',
            'preloadImage' => $preloadImage,
            'bodyClass' => ['page-product', 'product-' . $product['id']],

            'pageScripts' => [
                'resources/js/pages/product-single.js',
            ],
        ]);
    }
}
