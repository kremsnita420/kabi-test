<?php

declare(strict_types=1);

use App\Views\Helpers\Picture;

$e = static fn($v) => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');

$projectRoot = dirname(__DIR__, 3);
$publicDir   = $projectRoot . '/public';

$exists = static fn(string $p) => $p !== '' && is_file($publicDir . $p);
?>

<div class="grid container">
    <?php foreach ($products as $product): ?>

        <?php
        $list = $product['images']['list'] ?? [];

        $jpg   = (string) ($list['jpg']  ?? '');
        $jpg2  = (string) ($list['jpg2'] ?? '');
        $webp  = (string) ($list['webp'] ?? '');
        $webp2 = (string) ($list['webp2'] ?? '');

        $jpg2Ok  = $jpg2 !== '' && $exists($jpg2);
        $webpOk  = $webp !== '' && $exists($webp);
        $webp2Ok = $webp2 !== '' && $exists($webp2);

        $webpSrcset = '';
        if ($webpOk)
        {
            $webpSrcset = $webp2Ok
                ? "$webp 1x, $webp2 2x"
                : "$webp 1x";
        }
        ?>

        <div class="card">

            <div class="card-image">
                <a href="/product/<?= (int)$product['id'] ?>">
                    <?= Picture::render(
                        $product['images']['list'] ?? [],
                        [
                            'alt' => (string)($product['name'] ?? ''),
                            'loading' => 'lazy',
                            'decoding' => 'async',
                            'width' => 520,
                            'height' => 520,
                            'publicDir' => $publicDir,
                        ]
                    ) ?>
                </a>
            </div>

            <div class="card-body">
                <h2 class="card-title"><?= $e($product['name']) ?></h2>
                <h4 class="card-category"><?= $e($product['category']) ?></h4>

                <p class="card-desc">
                    <?= $e($product['short_description']) ?>
                </p>

                <div class="card-footer">
                    <a class="btn" href="/product/<?= (int)$product['id'] ?>">
                        <i class="fa-solid fa-plus"></i> Več o izdelku
                    </a>
                </div>
            </div>

        </div>

    <?php endforeach; ?>
</div>