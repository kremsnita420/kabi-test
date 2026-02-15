<?php
// src/Views/layout.php

declare(strict_types=1);

$projectRoot = dirname(__DIR__, 2); // src/Views -> project root
$viteDev = is_file($projectRoot . '/.vite-dev');

// defaults
$title = $title ?? 'Kabi Test';
$head  = $head ?? '';
$preloadImage = $preloadImage ?? null;

// Optional: page-specific Vite module entrypoints (dev) / built JS (prod)
// Example:
// $pageScripts = ['resources/js/pages/admin-upload.js'];
$pageScripts = $pageScripts ?? [];

?>
<!DOCTYPE html>
<html lang="sl">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars((string) $title, ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="icon" href="/assets/images/favicon.ico">
    <!-- Static vendor CSS -->
    <link rel="stylesheet" href="/assets/fontawesome/css/all.min.css">

    <?= $head ?>

    <?php if (!empty($preloadImage)): ?>
        <link rel="preload" as="image" href="<?= htmlspecialchars((string) $preloadImage, ENT_QUOTES, 'UTF-8') ?>" fetchpriority="high">
    <?php endif; ?>

    <?php if ($viteDev): ?>
        <!-- Vite dev -->
        <script src="/build/js/app.js"></script>

        <?php foreach (($pageScripts ?? []) as $entry): ?>
            <?php if ($entry === 'resources/js/pages/admin-upload.js'): ?>
                <script src="/build/js/admin-upload.js"></script>
            <?php endif; ?>
        <?php endforeach; ?>
        <script type="module" src="http://localhost:5173/@vite/client"></script>
        <link rel="stylesheet" href="http://localhost:5173/resources/scss/style.scss">
        <script type="module" src="http://localhost:5173/resources/js/app.js"></script>

        <?php foreach ($pageScripts as $entry): ?>
            <script type="module" src="http://localhost:5173/<?= htmlspecialchars((string) $entry, ENT_QUOTES, 'UTF-8') ?>"></script>
        <?php endforeach; ?>
    <?php else: ?>
        <!-- Production -->
        <link rel="stylesheet" href="/build/css/style.css">
    <?php endif; ?>
</head>

<body>

    <?php require __DIR__ . '/partials/header.php'; ?>

    <main>
        <?php require $viewFile; ?>
    </main>

    <?php require __DIR__ . '/partials/footer.php'; ?>

    <?php if (!$viteDev): ?>
        <script src="/build/js/app.js"></script>

        <?php
        // Map page scripts to built equivalents if you keep stable filenames.
        // For admin-upload we expect /build/js/admin-upload.js
        foreach ($pageScripts as $entry)
        {
            if ($entry === 'resources/js/pages/admin-upload.js')
            {
                echo '<script src="/build/js/admin-upload.js"></script>';
            }
        }
        ?>
    <?php endif; ?>

</body>

</html>