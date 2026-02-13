<?php
// src/Views/layout.php

declare(strict_types=1);

// Robust dev/prod switch for Apache:
// - DEV (HMR): create a `.vite-dev` file in project root and run `npm run dev`
// - PROD: remove `.vite-dev` and run `npm run build`
//
// Build output is served from:
// - /public/build/*  (compiled JS/CSS)
// Static vendor assets (fonts, fontawesome, images) live in:
// - /public/assets/*
$projectRoot = dirname(__DIR__, 2); // src/Views -> project root
$viteDev = is_file($projectRoot . '/.vite-dev');
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title ?? 'Kabi Test', ENT_QUOTES) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?= $head ?? '' ?>

    <!-- Static vendor CSS -->
    <link rel="stylesheet" href="/assets/fontawesome/css/all.min.css">

    <?php if ($viteDev): ?>
        <link rel="stylesheet" href="http://localhost:5173/resources/scss/style.scss">
        <script type="module" src="http://localhost:5173/@vite/client"></script>
        <script type="module" src="http://localhost:5173/resources/js/app.js"></script>
    <?php else: ?>
        <link rel="stylesheet" href="/build/css/style.css">
    <?php endif; ?>
</head>



<body>

    <?php require __DIR__ . '/partials/header.php'; ?>

    <main class="container">
        <?php require $viewFile; ?>
    </main>

    <?php require __DIR__ . '/partials/footer.php'; ?>

    <?php if (!$viteDev): ?>
        <script src="/build/js/app.js"></script>
    <?php endif; ?>

</body>

</html>