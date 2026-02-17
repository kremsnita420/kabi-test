<?php

declare(strict_types=1);

namespace App\Support;

final class UrlGenerator
{
    private string $defaultLang = 'sl';

    /** If set, overrides language auto-detected from URL. */
    private ?string $forcedLang = null;

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

    /**
     * Return a cloned generator that builds URLs for a specific language.
     */
    public function forLang(string $lang): self
    {
        $clone = clone $this;
        $clone->forcedLang = in_array($lang, $this->langs, true) ? $lang : $this->defaultLang;
        return $clone;
    }

    /**
     * Get the currently active language (forced or auto-detected).
     */
    public function lang(): string
    {
        return $this->forcedLang ?? $this->currentLang();
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

    /**
     * Build the equivalent URL in another language for the current request.
     * Falls back to target home if it cannot map.
     */
    public function switchTo(string $targetLang, ?string $currentUri = null): string
    {
        $targetLang = in_array($targetLang, $this->langs, true) ? $targetLang : $this->defaultLang;

        $uri  = $currentUri ?? ($_SERVER['REQUEST_URI'] ?? '/');
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $path = '/' . ltrim($path, '/');
        $path = rtrim($path, '/');
        if ($path === '')
        {
            $path = '/';
        }

        // Strip language prefix
        $segments = array_values(array_filter(explode('/', trim($path, '/')), static fn($s) => $s !== ''));
        if (!empty($segments) && in_array($segments[0], $this->langs, true) && $segments[0] !== $this->defaultLang)
        {
            array_shift($segments);
        }

        $rest = '/' . implode('/', $segments);
        $rest = rtrim($rest, '/');
        if ($rest === '')
        {
            $rest = '/';
        }

        // Home
        if ($rest === '/' || $rest === '')
        {
            return $this->forLang($targetLang)->home();
        }

        // Product detail: match any known product prefix in any language
        $prefixMap = $this->routes['product_prefix'] ?? [];
        foreach ($prefixMap as $pfx)
        {
            $pfx = '/' . trim((string) $pfx, '/');
            if (preg_match('#^' . preg_quote($pfx, '#') . '/(\d+)$#', $rest, $m))
            {
                return $this->forLang($targetLang)->product((int) $m[1]);
            }
        }

        // Static pages
        foreach (['about', 'contact', 'write_us', 'products'] as $key)
        {
            $map = $this->routes[$key] ?? [];
            foreach ($map as $slug)
            {
                $slugPath = '/' . trim((string) $slug, '/');
                if ($slugPath !== '/' && $rest === $slugPath)
                {
                    $gen = $this->forLang($targetLang);
                    return match ($key) {
                        'about' => $gen->about(),
                        'contact' => $gen->contact(),
                        'write_us' => $gen->writeUs(),
                        'products' => $gen->products(),
                        default => $gen->home(),
                    };
                }
            }
        }

        return $this->forLang($targetLang)->home();
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
        $lang = $this->lang();

        $langSegment = $lang !== $this->defaultLang
            ? $lang . '/'
            : '';

        $path = ltrim($path, '/');

        return '/' . $langSegment . $path;
    }

    private function resolve(string $key): string
    {
        $lang = $this->lang();

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
