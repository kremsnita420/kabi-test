<?php

declare(strict_types=1);

namespace App\Services;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
// If using Imagick instead:
// use Intervention\Image\Drivers\Imagick\Driver;

final class ImageService
{
    private ImageManager $manager;
    private string $publicDir;

    public function __construct(string $publicDir)
    {
        $this->publicDir = rtrim($publicDir, '/');

        $this->manager = new ImageManager(
            new Driver() // switch to Imagick Driver() if available
        );
    }

    public function generateAll(string $webPath): array
    {
        $sourceAbs = $this->publicDir . $webPath;

        if (!is_file($sourceAbs))
        {
            throw new \RuntimeException("Source image not found: {$webPath}");
        }

        $dir = dirname($webPath);
        $name = pathinfo($webPath, PATHINFO_FILENAME);

        return [
            'hero' => $this->generateVariantSet(
                $sourceAbs,
                "{$dir}/hero-{$name}",
                1000,
                700
            ),
            'thumb' => $this->generateVariantSet(
                $sourceAbs,
                "{$dir}/thumb-{$name}",
                300,
                300
            ),
        ];
    }

    private function generateVariantSet(
        string $sourceAbs,
        string $baseWebPath,
        int $width,
        int $height
    ): array
    {
        $image = $this->manager->read($sourceAbs);

        // HERO / THUMB normal
        $image->cover($width, $height);

        $jpgPath = "{$baseWebPath}.jpg";
        $webpPath = "{$baseWebPath}.webp";

        $image->toJpeg(85)->save($this->publicDir . $jpgPath);
        $image->toWebp(85)->save($this->publicDir . $webpPath);

        // 2x
        $image2x = $this->manager->read($sourceAbs)
            ->cover($width * 2, $height * 2);

        $jpg2Path = "{$baseWebPath}@2x.jpg";
        $webp2Path = "{$baseWebPath}@2x.webp";

        $image2x->toJpeg(85)->save($this->publicDir . $jpg2Path);
        $image2x->toWebp(85)->save($this->publicDir . $webp2Path);

        return [
            'jpg'   => $jpgPath,
            'jpg2'  => $jpg2Path,
            'webp'  => $webpPath,
            'webp2' => $webp2Path,
        ];
    }
}
