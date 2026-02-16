<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Bulk Image Pipeline (Intervention Image v3)
| Generates centered square images:
|
| hero       1000 x 1000
| hero@2x    2000 x 2000
| thumb       140 x 140
| thumb@2x    280 x 280
|
| + WebP versions
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../vendor/autoload.php';

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
// Prefer Imagick if available:
// use Intervention\Image\Drivers\Imagick\Driver;

$projectRoot = dirname(__DIR__);
$public      = $projectRoot . '/public';
$base        = $public . '/assets/product-images';

$heroSize  = 1000;
$thumbSize = 140;

if (!is_dir($base))
{
    fwrite(STDERR, "Missing folder: {$base}\n");
    exit(1);
}

$manager = new ImageManager(new Driver());

$rii = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($base, FilesystemIterator::SKIP_DOTS)
);

$generated = 0;
$skipped   = 0;
$failed    = 0;

foreach ($rii as $file)
{

    if ($file->isDir()) continue;

    $src = $file->getPathname();

    // Only originals
    if (!preg_match('/\.(jpg|jpeg|png)$/i', $src)) continue;
    if (str_contains($src, '@2x')) continue;
    if (str_contains($src, 'hero-') || str_contains($src, 'thumb-')) continue;

    try
    {
        $did = processImage($manager, $src, $heroSize, $thumbSize);
        $did ? $generated++ : $skipped++;
    }
    catch (Throwable $e)
    {
        $failed++;
        fwrite(STDERR, "FAIL {$src}: {$e->getMessage()}\n");
    }
}

echo "✔ Pipeline finished\n";
echo "Generated: {$generated}\n";
echo "Skipped:   {$skipped}\n";
echo "Failed:    {$failed}\n";


/* ------------------------------------------------------------- */

function processImage(
    ImageManager $manager,
    string $src,
    int $heroSize,
    int $thumbSize
): bool
{

    $dir  = dirname($src);
    $name = pathinfo($src, PATHINFO_FILENAME);

    $targets = [
        "{$dir}/hero-{$name}.jpg"     => [$heroSize, $heroSize],
        "{$dir}/hero-{$name}@2x.jpg"  => [$heroSize * 2, $heroSize * 2],
        "{$dir}/thumb-{$name}.jpg"    => [$thumbSize, $thumbSize],
        "{$dir}/thumb-{$name}@2x.jpg" => [$thumbSize * 2, $thumbSize * 2],
    ];

    // Skip if everything already exists (jpg + webp)
    foreach ($targets as $jpg => $size)
    {
        $webp = preg_replace('/\.jpg$/', '.webp', $jpg);
        if (!is_file($jpg) || !is_file($webp))
        {
            goto generate;
        }
    }

    return false;

    generate:

    foreach ($targets as $destJpg => [$w, $h])
    {
        makeSquare($manager, $src, $destJpg, $w, $h);
        makeWebp($manager, $destJpg);
    }

    return true;
}

function makeSquare(
    ImageManager $manager,
    string $src,
    string $destJpg,
    int $w,
    int $h
): void
{

    if (is_file($destJpg)) return;

    $img = $manager->read($src);

    /*
     |--------------------------------------------------------------------------
     | cover() = resize + crop to fill target box
     | Default focal point is CENTER (what you asked for)
     |--------------------------------------------------------------------------
     */
    $img->cover($w, $h);

    // Flatten transparency nicely for PNG → JPG
    // (prevents black background artifacts)
    if (str_ends_with(strtolower($src), '.png'))
    {
        $img->fill('#ffffff');
    }

    $img->toJpeg(85)->save($destJpg);
}

function makeWebp(ImageManager $manager, string $jpgPath): void
{
    $webp = preg_replace('/\.jpg$/', '.webp', $jpgPath);

    if (is_file($webp)) return;

    $img = $manager->read($jpgPath);
    $img->toWebp(80)->save($webp);
}
