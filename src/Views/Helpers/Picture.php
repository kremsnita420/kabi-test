<?php

declare(strict_types=1);

namespace App\Views\Helpers;

final class Picture
{
    /**
     * Render a <picture> tag from a variant set.
     *
     * Expected variant keys (any subset is OK):
     *  - webp, webp2
     *  - jpg,  jpg2
     *
     * Options:
     *  - alt (string)
     *  - class (string)
     *  - width (int|string)
     *  - height (int|string)
     *  - loading (lazy|eager)
     *  - decoding (async|auto|sync)
     *  - fetchpriority (high|low|auto)
     *  - sizes (string)    (optional, for responsive srcset)
     *  - id (string)       (optional)
     *  - attrs (array)     (extra attributes for <img>)
     *  - sourceAttrs (array) (extra attributes for <source>)
     *  - publicDir (string|null) if provided, will omit non-existent files to prevent 404s
     */
    public static function render(array $variants, array $options = []): string
    {
        $alt          = (string)($options['alt'] ?? '');
        $class        = (string)($options['class'] ?? '');
        $id           = (string)($options['id'] ?? '');
        $width        = $options['width'] ?? null;
        $height       = $options['height'] ?? null;
        $loading      = (string)($options['loading'] ?? 'lazy');
        $decoding     = (string)($options['decoding'] ?? 'async');
        $fetchpriority = (string)($options['fetchpriority'] ?? '');
        $sizes        = (string)($options['sizes'] ?? '');

        $imgAttrs     = (array)($options['attrs'] ?? []);
        $sourceAttrs  = (array)($options['sourceAttrs'] ?? []);
        $publicDir    = $options['publicDir'] ?? null;

        // Normalize and optionally filter by existence
        $jpg  = self::pick($variants, 'jpg',  $publicDir);
        $jpg2 = self::pick($variants, 'jpg2', $publicDir);
        $webp = self::pick($variants, 'webp', $publicDir);
        $webp2 = self::pick($variants, 'webp2', $publicDir);

        // Must have at least a fallback raster
        if ($jpg === '')
        {
            return '';
        }

        $img = [];
        if ($id !== '')    $img['id'] = $id;
        if ($class !== '') $img['class'] = $class;
        $img['src'] = $jpg;

        if ($jpg2 !== '')
        {
            $img['srcset'] = $jpg . ' 1x, ' . $jpg2 . ' 2x';
        }

        if ($sizes !== '')
        {
            $img['sizes'] = $sizes;
        }

        if ($width !== null)  $img['width']  = (string)$width;
        if ($height !== null) $img['height'] = (string)$height;

        if ($loading !== '')   $img['loading'] = $loading;
        if ($decoding !== '')  $img['decoding'] = $decoding;
        if ($fetchpriority !== '') $img['fetchpriority'] = $fetchpriority;

        $img['alt'] = $alt;

        // Merge extra <img> attrs (caller wins)
        foreach ($imgAttrs as $k => $v)
        {
            $img[(string)$k] = (string)$v;
        }

        $sourceTag = '';
        if ($webp !== '')
        {
            $srcset = $webp2 !== '' ? ($webp . ' 1x, ' . $webp2 . ' 2x') : ($webp . ' 1x');
            $source = array_merge([
                'type' => 'image/webp',
                'srcset' => $srcset,
            ], self::stringifyMap($sourceAttrs));

            $sourceTag = '<source' . self::attrs($source) . '>';
        }

        return '<picture>' . $sourceTag . '<img' . self::attrs($img) . '></picture>';
    }

    private static function pick(array $variants, string $key, $publicDir): string
    {
        $v = (string)($variants[$key] ?? '');
        if ($v === '')
        {
            return '';
        }

        if (is_string($publicDir) && $publicDir !== '')
        {
            $abs = rtrim($publicDir, '/') . $v;
            if (!is_file($abs))
            {
                return '';
            }
        }

        return $v;
    }

    private static function attrs(array $attrs): string
    {
        $out = '';
        foreach ($attrs as $k => $v)
        {
            if ($v === '' || $v === null) continue;
            $out .= ' ' . self::e((string)$k) . '="' . self::e((string)$v) . '"';
        }
        return $out;
    }

    private static function stringifyMap(array $map): array
    {
        $out = [];
        foreach ($map as $k => $v)
        {
            $out[(string)$k] = (string)$v;
        }
        return $out;
    }

    private static function e(string $s): string
    {
        return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
    }
}
