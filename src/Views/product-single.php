<h1><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></h1>

<p><?= htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8') ?></p>
<p><?= number_format($product['price'], 2) ?> €</p>

<a href="/products">Nazaj</a>
