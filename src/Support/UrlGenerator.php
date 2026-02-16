<?php

declare(strict_types=1);

namespace App\Support;

final class UrlGenerator
{
    private string $defaultLang = 'sl';

    /**
     * Supported languages.
     * Convention: default language has no "/sl" prefix; others use "/{lang}/...".
     */
    private array $langs = ['sl', 'en', 'de', 'hr'];

    /**
     * Localized slugs (single source of truth).
     *
     * - For "home", use empty string '' as the slug.
     * - Product detail uses a prefix slug (e.g. "izdelek") which is then combined with "/{id}".
     */
    private array $routes = [
        'home' => [
            'sl' => '',
            'en' => '',
            'de' => '',
            'hr' => '',
        ],

        'about' => [
            'sl' => 'o-nas',
            'en' => 'about',
            'de' => 'uber-uns',
            'hr' => 'o-nama',
        ],

        'contact' => [
            'sl' => 'kontakt',
            'en' => 'contact',
            'de' => 'kontakt',
            'hr' => 'kontakt',
        ],

        'write_us' => [
            'sl' => 'pisite-nam',
            'en' => 'write-to-us',
            'de' => 'schreiben-sie-uns',
            'hr' => 'pisite-nam',
        ],

        'products' => [
            'sl' => 'izdelki',
            'en' => 'products',
            'de' => 'produkte',
            'hr' => 'proizvodi',
        ],

        'product_prefix' => [
            'sl' => 'izdelek',
            'en' => 'product',
            'de' => 'produkt',
            'hr' => 'proizvod',
        ],
    ];

    /* ==========================
       URL generation (current request language)
       ========================== */

    public function home(): string
    {
        return $this->build($this->resolve('home'));
    }

    public function about(): string
    {
        return $this->build($this->resolve('about'));
    }

    public function contact(): string
    {
        return $this->build($this->resolve('contact'));
    }

    public function writeUs(): string
    {
        return $this->build($this->resolve('write_us'));
    }

    public function products(): string
    {
        return $this->build($this->resolve('products'));
    }

    public function product(int $id): string
    {
        $prefix = $this->resolve('product_prefix');
        return $this->build($prefix . '/' . $id);
    }

    /* ==========================
       Route registration helpers (ALL language variants)
       ========================== */

    /**
     * Returns all localized path variants for a named route, e.g.:
     * - home: ['/', '/en', '/de', '/hr']
     * - products: ['/izdelki', '/en/products', '/de/produkte', '/hr/proizvodi']
     */
    public function paths(string $routeKey): array
    {
        $out = [];

        foreach ($this->langs as $lang)
        {
            $slug = $this->routes[$routeKey][$lang] ?? $this->routes[$routeKey][$this->defaultLang] ?? '';

            // Default lang => no /sl prefix
            if ($lang === $this->defaultLang)
            {
                $out[] = $slug === '' ? '/' : '/' . ltrim($slug, '/');
                continue;
            }

            // Non-default langs => /{lang}/... (home becomes "/{lang}")
            $out[] = $slug === ''
                ? '/' . $lang
                : '/' . $lang . '/' . ltrim($slug, '/');
        }

        return $out;
    }

    /**
     * Returns all localized product detail route patterns with {id} param:
     * ['/izdelek/{id:\d+}', '/en/product/{id:\d+}', ...]
     */
    public function productShowPatterns(string $idPattern = '{id:\d+}'): array
    {
        $out = [];

        foreach ($this->langs as $lang)
        {
            $prefix = $this->routes['product_prefix'][$lang] ?? $this->routes['product_prefix'][$this->defaultLang];

            if ($lang === $this->defaultLang)
            {
                $out[] = '/' . $prefix . '/' . $idPattern;
                continue;
            }

            $out[] = '/' . $lang . '/' . $prefix . '/' . $idPattern;
        }

        return $out;
    }

    /* ==========================
       Internal helpers
       ========================== */

    private function build(string $path): string
    {
        $lang = $this->currentLang();

        $langSegment = $lang !== $this->defaultLang
            ? $lang . '/'
            : '';

        $path = ltrim($path, '/');

        return '/' . $langSegment . $path;
    }

    private function resolve(string $key): string
    {
        $lang = $this->currentLang();

        $map = $this->routes[$key] ?? [];

        return $map[$lang]
            ?? $map[$this->defaultLang]
            ?? '';
    }

    private function currentLang(): string
    {
        $uri  = $_SERVER['REQUEST_URI'] ?? '/';
        $path = trim(parse_url($uri, PHP_URL_PATH) ?? '/', '/');

        if ($path === '')
        {
            return $this->defaultLang;
        }

        $first = explode('/', $path, 2)[0];

        return in_array($first, $this->langs, true)
            ? $first
            : $this->defaultLang;
    }
}
