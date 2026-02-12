<?php

declare(strict_types=1);

// Dev/prod asset loading:
// - In dev: set VITE_DEV=1 and run `npm run dev` to load assets from Vite (HMR/live reload).
// - In prod: run `npm run build` and assets are served from /public/assets/*.
$viteDev = file_exists(__DIR__ . '/../../.vite-dev');

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Products</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php if ($viteDev): ?>
        <script type="module" src="http://localhost:5173/@vite/client"></script>
        <script type="module" src="http://localhost:5173/resources/js/app.js"></script>
    <?php else: ?>
        <link rel="stylesheet" href="/assets/css/style.css">
    <?php endif; ?>
</head>

<body>

    <header>
        <a href="/products">Product List</a>
    </header>

    <main>
        <?php require $viewFile; ?>
    </main>

    <?php if (!$viteDev): ?>
        <script src="/assets/js/app.js"></script>
    <?php endif; ?>

</body>

</html>