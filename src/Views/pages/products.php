<?php

declare(strict_types=1);

// This view displays the list of products. It expects a `$products` array
// containing product data as provided by the controller. The existing
// partial `product-list.php` is reused to render the actual grid of cards.

$e = static fn($v) => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');

// Ensure we have a products array to pass to the partial
$products = $products ?? [];

?>

<div class="container">


    <?php
    // Reuse the product-list partial to render the product grid
    require __DIR__ . '/../partials/product-list.php';
    ?>
</div>