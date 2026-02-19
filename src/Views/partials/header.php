<?php

declare(strict_types=1);

use App\Support\UrlGenerator;
use App\Support\I18n;

// Determine the current request path for active nav highlighting
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$isActive = static function (string $href) use ($path): string
{
    return $path === $href ? ' is-active' : '';
};

// Instantiate URL generator to build localized route paths
$url = new UrlGenerator();
$currentLang = $url->lang();
$langs = ['sl', 'en', 'de', 'hr'];
?>

<header class="site-header container" role="banner">
    <a href="<?= $url->home() ?>" class="site-logo" aria-label="<?= I18n::et('nav.home') ?>">
        <img
            src="/assets/images/logo.png"
            srcset="/assets/images/logo.png 1x, /assets/images/logo@2x.png 2x"
            alt="<?= I18n::et('site.name') ?>"
            width="160"
            height="48"
            loading="eager"
            decoding="async">
    </a>
    <nav class="top-nav" aria-label="<?= I18n::et('nav.menu') ?>">

        <!-- Language switcher -->
        <?php require __DIR__ . '/language-switcher.php'; ?>

        <button
            class="nav-toggle"
            type="button"
            aria-label="<?= I18n::et('nav.open_menu') ?>"
            aria-controls="site-nav"
            aria-expanded="false">
            <span class="nav-toggle__bar" aria-hidden="true"></span>
            <span class="nav-toggle__bar" aria-hidden="true"></span>
            <span class="nav-toggle__bar" aria-hidden="true"></span>
        </button>

        <!-- ONE nav container for both desktop + mobile -->
        <div id="site-nav" class="nav-links" data-nav>
            <div class="nav-panel" role="dialog" aria-modal="true" aria-label="Meni">
                <div class="nav-panel__header">
                    <span class="nav-panel__title"><?= I18n::et('nav.menu') ?></span>

                </div>

                <div class="nav-panel__links">
                    <a class="nav-item<?= $isActive($url->home()) ?>" href="<?= $url->home() ?>"><?= I18n::et('nav.home') ?></a>
                    <a class="nav-item<?= $isActive($url->about()) ?>" href="<?= $url->about() ?>"><?= I18n::et('nav.about') ?></a>
                    <a class="nav-item<?= $isActive($url->contact()) ?>" href="<?= $url->contact() ?>"><?= I18n::et('nav.contact') ?></a>
                    <a class="nav-item<?= $isActive($url->writeUs()) ?>" href="<?= $url->writeUs() ?>"><?= I18n::et('nav.write_us') ?></a>
                    <a class="nav-item <?= $isActive($url->products()) ?>" href="<?= $url->products() ?>"><?= I18n::et('nav.products') ?></a>
                </div>
            </div>
        </div>
    </nav>
</header>