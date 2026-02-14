<div class="grid">
    <?php foreach ($products as $product): ?>
        <div class="card">

            <div class="card-image">
                <a class="btn" href="/product/<?= (int) $product['id'] ?>">
                    <img
                        src="<?= htmlspecialchars($product['image'], ENT_QUOTES, 'UTF-8') ?>"
                        alt="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>"
                        loading="lazy">
                </a>

            </div>

            <div class="card-body">
                <h2 class="card-title"><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></h2>
                <h4 class="card-category"><?= htmlspecialchars($product['category'], ENT_QUOTES, 'UTF-8') ?></h4>
                <p class="card-desc">
                    <?= htmlspecialchars($product['short_description'], ENT_QUOTES, 'UTF-8') ?>
                </p>

                <div class="card-footer">
                    <!-- <span class="price">
                        <?= number_format($product['price'], 2) ?> €
                    </span> -->

                    <a class="btn" href="/product/<?= (int) $product['id'] ?>">
                        <i class="fa-solid fa-plus"></i> Več o izdelku
                    </a>
                </div>
            </div>

        </div>
    <?php endforeach; ?>
</div>