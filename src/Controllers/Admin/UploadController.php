<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

// use App\Services\ImagePipeline;

use App\Services\ImageService;

final class UploadController
{
    public function store(string $slug): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $slug = strtolower(trim($slug));
        if (!preg_match('/^[a-z0-9\-]+$/', $slug))
        {
            http_response_code(400);
            echo json_encode(['ok' => false, 'error' => 'Invalid product slug']);
            return;
        }

        if (empty($_FILES['image']) || !is_array($_FILES['image']))
        {
            http_response_code(400);
            echo json_encode(['ok' => false, 'error' => 'Missing file field: image']);
            return;
        }

        $file = $_FILES['image'];

        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK)
        {
            http_response_code(400);
            echo json_encode(['ok' => false, 'error' => 'Upload error: ' . (int)($file['error'] ?? -1)]);
            return;
        }

        $tmpPath  = (string)($file['tmp_name'] ?? '');
        $origName = (string)($file['name'] ?? '');

        if ($tmpPath === '' || !is_uploaded_file($tmpPath))
        {
            http_response_code(400);
            echo json_encode(['ok' => false, 'error' => 'Invalid upload']);
            return;
        }

        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png'], true))
        {
            http_response_code(415);
            echo json_encode(['ok' => false, 'error' => 'Only JPG/JPEG/PNG allowed']);
            return;
        }

        $info = @getimagesize($tmpPath);
        if (!$info)
        {
            http_response_code(415);
            echo json_encode(['ok' => false, 'error' => 'Uploaded file is not a valid image']);
            return;
        }

        // Project paths
        $projectRoot = dirname(__DIR__, 3); // src/Controllers/Admin -> project root
        $publicDir   = $projectRoot . '/public';

        // Destination folder for this product
        $productDirAbs = $publicDir . '/assets/product-images/' . $slug;
        $productDirWeb = '/assets/product-images/' . $slug;

        if (!is_dir($productDirAbs))
        {
            mkdir($productDirAbs, 0775, true);
        }

        // Decide next numeric filename: 1.jpg, 2.jpg, 3.jpg...
        $nextIndex = $this->nextImageIndex($productDirAbs);

        // Save original as N.jpg (normalize jpeg->jpg)
        $origExt = ($ext === 'jpeg') ? 'jpg' : $ext;
        $origAbs = $productDirAbs . '/' . $nextIndex . '.' . $origExt;
        $origWeb = $productDirWeb . '/' . $nextIndex . '.' . $origExt;

        if (!move_uploaded_file($tmpPath, $origAbs))
        {
            http_response_code(500);
            echo json_encode(['ok' => false, 'error' => 'Failed to store uploaded file']);
            return;
        }

        // If PNG uploaded, convert original to JPG for consistent pipeline naming
        if ($origExt === 'png')
        {
            $jpgAbs = $productDirAbs . '/' . $nextIndex . '.jpg';
            $jpgWeb = $productDirWeb . '/' . $nextIndex . '.jpg';

            $this->pngToJpg($origAbs, $jpgAbs, 90);
            // keep original png if you want; or delete to avoid duplicates:
            // @unlink($origAbs);

            $origAbs = $jpgAbs;
            $origWeb = $jpgWeb;
        }

        // Generate hero/thumb variants + webp
        try
        {
            // $pipeline = new ImagePipeline($publicDir);
            // $variants = $pipeline->generateAll($origWeb);
            $imageService = new ImageService($publicDir);
            $variants = $imageService->generateAll($origWeb);
        }
        catch (\Throwable $e)
        {
            http_response_code(500);
            echo json_encode([
                'ok' => false,
                'error' => 'Image processing failed: ' . $e->getMessage(),
                'original' => $origWeb,
            ]);
            return;
        }

        echo json_encode([
            'ok' => true,
            'slug' => $slug,
            'index' => $nextIndex,
            'original' => $origWeb,
            'variants' => $variants,
        ]);
    }

    private function nextImageIndex(string $dirAbs): int
    {
        $max = 0;
        foreach (glob($dirAbs . '/*.jpg') ?: [] as $path)
        {
            $base = basename($path);
            if (preg_match('/^(\d+)\.jpg$/', $base, $m))
            {
                $max = max($max, (int)$m[1]);
            }
        }
        // also consider png uploads if kept
        foreach (glob($dirAbs . '/*.png') ?: [] as $path)
        {
            $base = basename($path);
            if (preg_match('/^(\d+)\.png$/', $base, $m))
            {
                $max = max($max, (int)$m[1]);
            }
        }
        return $max + 1;
    }

    private function pngToJpg(string $srcPngAbs, string $destJpgAbs, int $quality): void
    {
        $img = @imagecreatefrompng($srcPngAbs);
        if (!$img)
        {
            throw new \RuntimeException('Failed to read PNG');
        }

        // White background for transparency
        $w = imagesx($img);
        $h = imagesy($img);
        $canvas = imagecreatetruecolor($w, $h);
        $white = imagecolorallocate($canvas, 255, 255, 255);
        imagefilledrectangle($canvas, 0, 0, $w, $h, $white);
        imagecopy($canvas, $img, 0, 0, 0, 0, $w, $h);

        imagejpeg($canvas, $destJpgAbs, $quality);

        imagedestroy($canvas);
        imagedestroy($img);
    }
}
