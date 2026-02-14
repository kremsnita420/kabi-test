<?php

declare(strict_types=1);

$e = static fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');

$name  = $product['name'] ?? 'Izdelek';
$price = isset($product['price']) ? (float) $product['price'] : 0.0;

$subtitle = $product['short_description'] ?? '';

$gallery = $product['gallery'] ?? [];
$image   = $product['image'] ?? ($gallery[0] ?? '');
if (!$gallery && $image)
{
    $gallery = [$image];
}

$main = $gallery[0] ?? $image;
?>

<section class="product-single">
    <div class="product-single__grid">

        <!-- LEFT: Gallery -->
        <div class="product-single__media">
            <div class="product-gallery">
                <div class="product-gallery__main">
                    <?php if ($main): ?>
                        <img
                            id="productMainImage"
                            src="<?= $e($main) ?>"
                            alt="<?= $e($name) ?>"
                            loading="eager"
                            decoding="async">
                    <?php else: ?>
                        <div class="product-gallery__placeholder" aria-hidden="true"></div>
                    <?php endif; ?>
                </div>

                <?php if (!empty($gallery)): ?>
                    <div class="product-gallery__thumbs" aria-label="Galerija">
                        <?php foreach ($gallery as $i => $img): ?>
                            <button
                                type="button"
                                class="product-gallery__thumb<?= $i === 0 ? ' is-active' : '' ?>"
                                data-img="<?= $e($img) ?>"
                                aria-label="Prikaži sliko <?= (int)($i + 1) ?>">
                                <img
                                    src="<?= $e($img) ?>"
                                    alt="<?= $e($name) ?> – slika <?= (int)($i + 1) ?>"
                                    loading="lazy"
                                    decoding="async">
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

            <div class="product-single__text">
                <!-- Your screenshot shows 2 paragraphs; we’ll split description into two blocks nicely -->
                <?php
                $desc = trim((string)($product['description'] ?? ''));
                if ($desc)
                {
                    // naive split: first sentence/half in p1, rest in p2 (good enough for demo)
                    $parts = preg_split('/(?<=[.!?])\s+/', $desc, 2);
                    $p1 = $parts[0] ?? $desc;
                    $p2 = $parts[1] ?? '';
                ?>
                    <p><?= $e($p1) ?></p>
                    <?php if ($p2): ?>
                        <p><?= $e($p2) ?></p>
                    <?php endif; ?>
                <?php } ?>
            </div>

            <div class="product-single__meta">
                <span class="product-single__price"><?= number_format($price, 2) ?> €</span>
            </div>

            <a class="btn btn--outline btn--back" href="/products">
                <i class="fa-solid fa-chevron-left"></i>
                Nazaj na seznam
            </a>
        </div>

    </div>
</section>