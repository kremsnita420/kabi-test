<?php
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$isActive = static function (string $href) use ($path): string
{
    return $path === $href ? ' is-active' : '';
};
?>

<header class="site-header container" role="banner">
    <a href="/" class="site-logo" aria-label="Homepage">
        <img
            src="/assets/images/logo.png"
            srcset="/assets/images/logo.png 1x, /assets/images/logo@2x.png 2x"
            alt="Kabi Test"
            width="160"
            height="48"
            loading="eager"
            decoding="async">
    </a>
    <nav class="top-nav" aria-label="Glavna navigacija">


        <button
            class="nav-toggle"
            type="button"
            aria-label="Odpri meni"
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
                    <span class="nav-panel__title">Meni</span>

                </div>

                <div class="nav-panel__links">
                    <a class="nav-item<?= $isActive('/') ?>" href="/">Domov</a>
                    <a class="nav-item<?= $isActive('/o-nas') ?>" href="/o-nas">O nas</a>
                    <a class="nav-item<?= $isActive('/kontakt') ?>" href="/kontakt">Kontakt</a>
                    <a class="nav-item<?= $isActive('/pisite-nam') ?>" href="/pisite-nam">Pišite nam</a>
                    <a class="nav-item <?= $isActive('/izdelki') ?>" href="/izdelki">Izdelki</a>
                </div>
            </div>
        </div>
    </nav>
</header>