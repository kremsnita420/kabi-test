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
            <div class="product-gallery">

                <div class="product-gallery__main">
                    <?php if ($mainJpg): ?>
                        <?php $first = $galleryItems[0]['hero'] ?? []; ?>

                        <?= Picture::render($first, [
                            'id' => 'productMainImage',
                            'alt' => $name,
                            'loading' => 'eager',
                            'decoding' => 'async',
                            'fetchpriority' => 'high',
                            'width' => 1200,
                            'height' => 1200,
                            'publicDir' => $publicDir,
                        ]) ?>
                    <?php else: ?>
                        <div class="product-gallery__placeholder" aria-hidden="true"></div>
                    <?php endif; ?>
                </div>

                <?php if (!empty($galleryItems)): ?>
                    <div class="product-gallery__thumbs" aria-label="Galerija">
                        <?php foreach ($galleryItems as $i => $item): ?>
                            <?php
                            $hero  = $item['hero']  ?? [];
                            $thumb = $item['thumb'] ?? [];

                            $heroJpg   = (string) ($hero['jpg']   ?? '');
                            $heroJpg2  = (string) ($hero['jpg2']  ?? '');
                            $heroWebp  = (string) ($hero['webp']  ?? '');
                            $heroWebp2 = (string) ($hero['webp2'] ?? '');

                            $thumbJpg   = (string) ($thumb['jpg']   ?? '');
                            $thumbJpg2  = (string) ($thumb['jpg2']  ?? '');
                            $thumbWebp  = (string) ($thumb['webp']  ?? '');
                            $thumbWebp2 = (string) ($thumb['webp2'] ?? '');

                            // Existence checks (avoid 404)
                            $heroJpg2Ok  = $heroJpg2 !== '' && $exists($heroJpg2);
                            $heroWebpOk  = $heroWebp !== '' && $exists($heroWebp);
                            $heroWebp2Ok = $heroWebp2 !== '' && $exists($heroWebp2);

                            $thumbJpg2Ok  = $thumbJpg2 !== '' && $exists($thumbJpg2);
                            $thumbWebpOk  = $thumbWebp !== '' && $exists($thumbWebp);
                            $thumbWebp2Ok = $thumbWebp2 !== '' && $exists($thumbWebp2);

                            // Build safe srcsets
                            $thumbWebpSrcset = '';
                            if ($thumbWebpOk)
                            {
                                $thumbWebpSrcset = $thumbWebp2Ok
                                    ? ($thumbWebp . ' 1x, ' . $thumbWebp2 . ' 2x')
                                    : ($thumbWebp . ' 1x');
                            }

                            // If thumb files are missing for some reason, fall back to hero jpg
                            $thumbFallback = $thumbJpg !== '' ? $thumbJpg : $heroJpg;
                            ?>
                            <button
                                type="button"
                                class="product-gallery__thumb<?= $i === 0 ? ' is-active' : '' ?>"
                                data-hero-jpg="<?= $e($heroJpg) ?>"
                                data-hero-jpg2="<?= $e($heroJpg2Ok ? $heroJpg2 : '') ?>"
                                data-hero-webp="<?= $e($heroWebpOk ? $heroWebp : '') ?>"
                                data-hero-webp2="<?= $e($heroWebp2Ok ? $heroWebp2 : '') ?>"
                                aria-label="Prikaži sliko <?= (int) ($i + 1) ?>">
                                <?= Picture::render($thumb, [
                                    'alt' => '',
                                    'loading' => 'lazy',
                                    'decoding' => 'async',
                                    'width' => 140,
                                    'height' => 140,
                                    'publicDir' => $publicDir,
                                ]) ?>

                            </button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

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