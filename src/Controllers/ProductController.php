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
        $this->render('product-list', compact('products'));
    }

    public function show(string $id): void
    {
        if (!ctype_digit($id)) {
            http_response_code(404);
            return;
        }

        $product = $this->repository->find((int)$id);

        if (!$product) {
            http_response_code(404);
            return;
        }

        $this->render('product-single', compact('product'));
    }
}
