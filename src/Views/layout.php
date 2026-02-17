<?php
// src/Views/layout.php

declare(strict_types=1);

use App\Support\I18n;

$projectRoot = dirname(__DIR__, 2); // src/Views -> project root
$viteDev     = is_file($projectRoot . '/.vite-dev');

// defaults
$title        = $title ?? 'Kabi Test';
$head         = $head ?? '';
$preloadImage = $preloadImage ?? null;

// Optional: page-specific entrypoints (dev) / mapped built JS (prod)
$pageScripts = $pageScripts ?? [];

/**
 * Map known Vite entrypoints to stable built filenames.
 * Adjust these if you add more entries in vite.config.js.
 */
$builtMap = [
    'resources/js/pages/admin-upload.js' => '/build/js/admin-upload.js',
];
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(I18n::lang(), ENT_QUOTES, 'UTF-8') ?>">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars((string) $title, ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="icon" href="/assets/images/favicon.ico">
    <link rel="stylesheet" href="/assets/fontawesome/css/all.min.css">

    <?= $head ?>

    <?php if (!empty($preloadImage)): ?>
        <link rel="preload" as="image"
            href="<?= htmlspecialchars((string) $preloadImage, ENT_QUOTES, 'UTF-8') ?>"
            fetchpriority="high">
    <?php endif; ?>

    <?php if ($viteDev): ?>
        <!-- ✅ DEV: load ONLY Vite (no /build assets) -->
        <script type="module" src="http://localhost:5173/@vite/client"></script>

        <!-- Load CSS via Vite (either via JS import or this link) -->
        <link rel="stylesheet" href="http://localhost:5173/resources/scss/style.scss">

        <!-- Main app entry -->
        <script type="module" src="http://localhost:5173/resources/js/app.js"></script>

        <!-- Optional per-page modules (dev) -->
        <?php foreach ($pageScripts as $entry): ?>
            <script type="module" src="http://localhost:5173/<?= htmlspecialchars((string) $entry, ENT_QUOTES, 'UTF-8') ?>"></script>
        <?php endforeach; ?>

    <?php else: ?>
        <!-- ✅ PROD: load ONLY built assets -->
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
        <!-- Main built JS -->
        <script src="/build/js/app.js"></script>

        <!-- Optional per-page built JS -->
        <?php foreach ($pageScripts as $entry): ?>
            <?php if (isset($builtMap[$entry])): ?>
                <script src="<?= htmlspecialchars((string) $builtMap[$entry], ENT_QUOTES, 'UTF-8') ?>"></script>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>

</body>

</html>