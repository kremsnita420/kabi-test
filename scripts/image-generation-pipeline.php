<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Bulk Image Pipeline (Intervention Image v3)
| Generates (width-only, keeps aspect ratio):
| - hero (1000w)
| - hero@2x (2000w)
| - thumb (140w)
| - thumb@2x (280w)
| + WebP versions
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../vendor/autoload.php';

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
// If you have Imagick installed, prefer:
// use Intervention\Image\Drivers\Imagick\Driver;

$projectRoot = dirname(__DIR__);
$public      = $projectRoot . '/public';
$base        = $public . '/assets/product-images';

$heroW  = 1000;
$thumbW = 140;

if (!is_dir($base))
{
    fwrite(STDERR, "Missing folder: {$base}\n");
    exit(1);
}

$manager = new ImageManager(new Driver());

$rii = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($base, FilesystemIterator::SKIP_DOTS)
);

$count = 0;
$skipped = 0;
$failed = 0;

foreach ($rii as $file)
{
    if ($file->isDir()) continue;

    $src = $file->getPathname();

    // only jpg/jpeg/png
    if (!preg_match('/\.(jpg|jpeg|png)$/i', $src)) continue;

    // skip generated
    if (str_contains($src, '@2x')) continue;
    if (str_contains($src, 'hero-') || str_contains($src, 'thumb-')) continue;

    // Process originals
    try
    {
        $did = process($manager, $src, $heroW, $thumbW);
        if ($did) $count++;
        else $skipped++;
    }
    catch (Throwable $e)
    {
        $failed++;
        fwrite(STDERR, "FAIL {$src}: {$e->getMessage()}\n");
    }
}

echo "✔ Image pipeline complete\n";
echo "Generated: {$count}, Skipped: {$skipped}, Failed: {$failed}\n";

function process(ImageManager $manager, string $src, int $heroW, int $thumbW): bool
{
    $dir  = dirname($src);
    $name = pathinfo($src, PATHINFO_FILENAME);

    $targets = [
        "{$dir}/hero-{$name}.jpg"      => $heroW,
        "{$dir}/hero-{$name}@2x.jpg"   => $heroW * 2,
        "{$dir}/thumb-{$name}.jpg"     => $thumbW,
        "{$dir}/thumb-{$name}@2x.jpg"  => $thumbW * 2,
    ];

    // If all outputs exist (jpg + webp), skip
    $allExist = true;
    foreach ($targets as $jpg => $w)
    {
        $webp = preg_replace('/\.jpg$/', '.webp', $jpg);
        if (!is_file($jpg) || !is_file($webp))
        {
            $allExist = false;
            break;
        }
    }
    if ($allExist)
    {
        return false;
    }

    foreach ($targets as $destJpg => $newW)
    {
        buildOne($manager, $src, $destJpg, $newW);
        buildWebpFromJpg($manager, $destJpg);
    }

    return true;
}

function buildOne(ImageManager $manager, string $src, string $destJpg, int $newW): void
{
    if (is_file($destJpg)) return;

    $img = $manager->read($src);

    // width-only resize, keep aspect ratio (no cropping)
    $img->resize($newW, null);

    // Save JPEG
    // If input is PNG with transparency, this will flatten; that matches your old pipeline intent.
    $img->toJpeg(85)->save($destJpg);
}

function buildWebpFromJpg(ImageManager $manager, string $jpgPath): void
{
    $webp = preg_replace('/\.jpg$/', '.webp', $jpgPath);
    if (is_file($webp)) return;

    $img = $manager->read($jpgPath);
    $img->toWebp(80)->save($webp);
}
