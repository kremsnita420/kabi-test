<?php

declare(strict_types=1);

$e = static fn($v) => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');

use App\Views\Helpers\Picture;

$projectRoot = dirname(__DIR__, 2);
$publicDir   = $projectRoot . '/public';

/**
 * Prevent 404s when some variants aren't generated yet.
 */
$projectRoot = dirname(__DIR__, 3); // src/Views/pages -> project root
$publicDir   = $projectRoot . '/public';

$exists = static fn(string $publicPath) => ($publicPath !== '' && is_file($publicDir . $publicPath));

$name     = $product['name'] ?? 'Izdelek';
$subtitle = $product['short_description'] ?? '';
$desc     = trim((string) ($product['description'] ?? ''));

// ✅ IMPORTANT: new structure created in ProductRepository::decorateImages()
$galleryItems = $product['gallery_items'] ?? [];

// Main image (hero of first gallery image)
$firstHero = $galleryItems[0]['hero'] ?? [];

$mainJpg   = (string) ($firstHero['jpg']  ?? ($product['image'] ?? ''));
$mainJpg2  = (string) ($firstHero['jpg2'] ?? '');
$mainWebp  = (string) ($firstHero['webp'] ?? '');
$mainWebp2 = (string) ($firstHero['webp2'] ?? '');

$mainJpg2Ok  = $mainJpg2 !== '' && $exists($mainJpg2);
$mainWebpOk  = $mainWebp !== '' && $exists($mainWebp);
$mainWebp2Ok = $mainWebp2 !== '' && $exists($mainWebp2);

$mainWebpSrcset = '';
if ($mainWebpOk)
{
    $mainWebpSrcset = $mainWebp2Ok
        ? ($mainWebp . ' 1x, ' . $mainWebp2 . ' 2x')
        : ($mainWebp . ' 1x');
}
?>

<section class="product-single container">
    <div class="product-single__grid">

        <!-- LEFT: Gallery -->
        <div class="product-single__media">
            <div class="product-gallery" data-product-gallery>

                <!-- Main slider -->
                <div class="swiper product-gallery__main" data-gallery-main>
                    <div class="swiper-wrapper">

                        <?php foreach ($galleryItems as $i => $item): ?>
                            <?php $hero = $item['hero'] ?? []; ?>

                            <div class="swiper-slide">
                                <?= Picture::render($hero, [
                                    'alt' => $name,
                                    'loading' => $i === 0 ? 'eager' : 'lazy',
                                    'decoding' => 'async',
                                    'fetchpriority' => $i === 0 ? 'high' : 'auto',
                                    'width' => 1200,
                                    'height' => 1200,
                                    'publicDir' => $publicDir,
                                ]) ?>
                            </div>
                        <?php endforeach; ?>

                    </div>

                    <!-- Optional navigation UI -->
                    <div class="product-gallery__nav product-gallery__prev" data-gallery-prev aria-label="Previous"></div>
                    <div class="product-gallery__nav product-gallery__next" data-gallery-next aria-label="Next"></div>
                </div>

                <!-- Thumbs slider -->
                <div class="swiper product-gallery__thumbs" data-gallery-thumbs>
                    <div class="swiper-wrapper">

                        <?php foreach ($galleryItems as $item): ?>
                            <?php $thumb = $item['thumb'] ?? []; ?>

                            <div class="swiper-slide product-gallery__thumb">
                                <?= Picture::render($thumb, [
                                    'alt' => '',
                                    'loading' => 'lazy',
                                    'decoding' => 'async',
                                    'width' => 140,
                                    'height' => 140,
                                    'publicDir' => $publicDir,
                                ]) ?>
                            </div>
                        <?php endforeach; ?>

                    </div>
                </div>

            </div>

        </div>

        <!-- RIGHT: Content -->
        <div class="product-single__content">
            <h1 class="product-single__title"><?= $e($name) ?></h1>

            <?php if ($subtitle): ?>
                <p class="product-single__subtitle"><?= $e($subtitle) ?></p>
            <?php endif; ?>

            <?php if ($desc): ?>
                <div class="product-single__text">
                    <?php
                    $parts = preg_split('/(?<=[.!?])\s+/', $desc, 2);
                    $p1 = $parts[0] ?? $desc;
                    $p2 = $parts[1] ?? '';
                    ?>
                    <p><?= $e($p1) ?></p>
                    <?php if ($p2): ?>
                        <p><?= $e($p2) ?></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <a class="btn btn--outline btn--back" href="/products">
                <i class="fa-solid fa-chevron-left"></i>
                Nazaj na seznam
            </a>
        </div>

    </div>
</section>