<h1>Products</h1>

<div class="grid">
    <?php foreach ($products as $product): ?>
        <div class="card">
            <h2><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></h2>
            <p><?= number_format($product['price'], 2) ?> €</p>
            <a href="/product/<?= $product['id'] ?>">Več</a>
        </div>
    <?php endforeach; ?>
</div>
