<?php
require __DIR__ . '/../vendor/autoload.php';

use Intervention\Image\ImageManager;

$manager = new ImageManager(['driver' => 'gd']);

$dir = __DIR__ . '/../public/assets/product-images';

$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

foreach ($files as $file)
{
    if ($file->isDir()) continue;

    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    if (in_array($ext, ['jpg', 'jpeg', 'png']))
    {
        $inputPath = $file->getPathname();
        $outputPath = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $inputPath);

        $manager->make($inputPath)
            ->encode('webp', 80)
            ->save($outputPath);

        echo "Converted: {$inputPath} → {$outputPath}\n";
    }
}
