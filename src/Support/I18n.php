<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Tiny i18n helper.
 *
 * - Language comes from UrlGenerator (URL prefix).
 * - Translations are simple PHP arrays in src/Translations/{lang}.php.
 * - In templates/controllers you can use I18n::t('key') / I18n::et('key').
 */
final class I18n
{
    private static string $lang = 'sl';

    /** @var array<string,string> */
    private static array $dict = [];

    public static function boot(string $lang): void
    {
        self::$lang = $lang;
        self::$dict = self::load($lang);
    }

    public static function lang(): string
    {
        return self::$lang;
    }

    /**
     * Translate a key.
     *
     * @param array<string, scalar> $vars
     */
    public static function t(string $key, array $vars = []): string
    {
        $text = self::$dict[$key] ?? $key;

        foreach ($vars as $k => $v)
        {
            $text = str_replace('{' . $k . '}', (string) $v, $text);
        }

        return $text;
    }

    /**
     * Translate and escape for HTML output.
     *
     * @param array<string, scalar> $vars
     */
    public static function et(string $key, array $vars = []): string
    {
        return htmlspecialchars(self::t($key, $vars), ENT_QUOTES, 'UTF-8');
    }

    /** @return array<string,string> */
    private static function load(string $lang): array
    {
        $base = dirname(__DIR__) . '/Translations/';

        $file = $base . $lang . '.php';
        if (is_file($file))
        {
            /** @var array<string,string> $dict */
            $dict = require $file;
            return $dict;
        }

        $fallback = $base . 'sl.php';
        if (is_file($fallback))
        {
            /** @var array<string,string> $dict */
            $dict = require $fallback;
            return $dict;
        }

        return [];
    }

    // add under: private static array $dict = [];
    /** @var array<string, array<string,string>> */
    private static array $cache = [];

    /**
     * Translate a key for a specific language (does NOT change global state).
     *
     * @param array<string, scalar> $vars
     */
    public static function tFor(string $lang, string $key, array $vars = []): string
    {
        $dict = self::$cache[$lang] ??= self::load($lang);

        $text = $dict[$key] ?? $key;

        foreach ($vars as $k => $v)
        {
            $text = str_replace('{' . $k . '}', (string) $v, $text);
        }

        return $text;
    }

    /**
     * Translate for a specific language and escape for HTML.
     *
     * @param array<string, scalar> $vars
     */
    public static function etFor(string $lang, string $key, array $vars = []): string
    {
        return htmlspecialchars(self::tFor($lang, $key, $vars), ENT_QUOTES, 'UTF-8');
    }
}
