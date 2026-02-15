<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\ProductRepository;

final class UploadHubController extends Controller
{
    public function index(): void
    {
        $repo = new ProductRepository();
        $products = $repo->all();

        // Only send what the UI needs
        $options = array_map(static fn(array $p) => [
            'id' => (int)($p['id'] ?? 0),
            'slug' => (string)($p['slug'] ?? ''),
            'name' => (string)($p['name'] ?? ''),
        ], $products);

        // Filter invalid
        $options = array_values(array_filter($options, static fn($o) => $o['slug'] !== '' && $o['name'] !== ''));

        $this->render('pages/admin/upload-hub', [
            'title' => 'Admin Upload – Kabi Test',
            'head'  => '<meta name="robots" content="noindex">',
            'products' => $options,
            'pageScripts' => [
                'resources/js/pages/admin-upload-hub.js',
            ],
        ]);
    }
}
