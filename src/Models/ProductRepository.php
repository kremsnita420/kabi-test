<?php

declare(strict_types=1);

namespace App\Models;

final class ProductRepository
{
    private array $products = [
        1 => [
            'id' => 1,
            "slug" => "headphones",
            'name' => 'Brezžične slušalke',
            'price' => 129.90,
            'category' => 'Avdio oprema',
            'short_description' => 'Poglobljen zvok z aktivnim odpravljanjem hrupa.',
            'description' => 'Visokokakovostne brezžične slušalke z naprednim sistemom za odpravljanje šumov, globokimi basi in do 30 ur delovanja baterije. Idealne za potovanja, delo in vsakodnevno poslušanje.',
            'image' => '/assets/product-images/headphones/main.jpg',
            'gallery' => [
                '/assets/product-images/headphones/1.jpg',
                '/assets/product-images/headphones/2.jpg',
                '/assets/product-images/headphones/3.jpg',
                '/assets/product-images/headphones/4.jpg',
            ],
        ],

        2 => [
            'id' => 2,
            "slug" => "watch",
            'name' => 'Pametna športna ura',
            'price' => 89.50,
            'category' => 'Pametne naprave',
            'short_description' => 'Spremljajte vadbo, srčni utrip in spanec.',
            'description' => 'Elegantna pametna ura s spremljanjem srčnega utripa v realnem času, analizo spanja, števcem korakov in več športnimi načini. Enostavna povezava s telefonom za obvestila.',
            'image' => '/assets/product-images/watch/main.jpg',
            'gallery' => [
                '/assets/product-images/watch/1.jpg',
                '/assets/product-images/watch/2.jpg',
                '/assets/product-images/watch/3.jpg',
                '/assets/product-images/watch/4.jpg',
            ],
        ],

        3 => [
            'id' => 3,
            "slug" => "speaker",
            'name' => 'Prenosni Bluetooth zvočnik',
            'price' => 59.99,
            'category' => 'Avdio oprema',
            'short_description' => 'Močan zvok v kompaktni obliki.',
            'description' => 'Kompakten Bluetooth zvočnik z bogatim zvokom, globokimi basi in do 12 ur predvajanja. Vodoodporna zasnova je popolna za uporabo na prostem.',
            'image' => '/assets/product-images/speaker/main.jpg',
            'gallery' => [
                '/assets/product-images/speaker/1.jpg',
                '/assets/product-images/speaker/2.jpg',
                '/assets/product-images/speaker/3.jpg',
                '/assets/product-images/speaker/4.jpg',
            ],
        ],

        4 => [
            'id' => 4,
            "slug" => "powerbank",
            'name' => 'Prenosna baterija USB-C',
            'price' => 39.90,
            'category' => 'Dodatki za telefone',
            'short_description' => 'Napolnite naprave kjerkoli in kadarkoli.',
            'description' => 'Visokozmogljiva prenosna baterija s kapaciteto 20.000 mAh in podporo hitremu polnjenju USB-C. Omogoča večkratno polnjenje telefonov, tablic in drugih naprav z vgrajeno zaščito.',
            'image' => '/assets/product-images/powerbank/main.jpg',
            'gallery' => [
                '/assets/product-images/powerbank/1.jpg',
                '/assets/product-images/powerbank/2.jpg',
                '/assets/product-images/powerbank/3.jpg',
                '/assets/product-images/powerbank/4.jpg',
            ],
        ],

        5 => [
            'id' => 5,
            "slug" => "chair",
            'name' => 'Ergonomski pisarniški stol',
            'price' => 189.00,
            'category' => 'Pisarniška oprema',
            'short_description' => 'Udobje za dolge delovne ure.',
            'description' => 'Kakovosten ergonomski stol z ledveno oporo, nastavljivimi nasloni za roke, zračno mrežasto hrbtišče in nastavljivo višino. Zasnovan za boljšo držo in manj bolečin v hrbtu.',
            'image' => '/assets/product-images/chair/main.jpg',
            'gallery' => [
                '/assets/product-images/chair/1.jpg',
                '/assets/product-images/chair/2.jpg',
                '/assets/product-images/chair/3.jpg',
                '/assets/product-images/chair/4.jpg',
            ],
        ],
    ];

    public function all(): array
    {
        $items = array_values($this->products);

        foreach ($items as &$p)
        {
            $p = $this->decorateImages($p);
        }

        return $items;
    }

    public function find(int $id): ?array
    {
        $p = $this->products[$id] ?? null;
        if (!$p)
        {
            return null;
        }

        return $this->decorateImages($p);
    }

    /**
     * Adds:
     *  - images: default hero/list/thumb variants derived from main image
     *  - gallery_items: per-image hero+thumb variants derived from each gallery image
     *
     * This keeps your old keys (`image`, `gallery`) intact for backwards compatibility.
     */
    private function decorateImages(array $product): array
    {
        $slug = (string)($product['slug'] ?? '');
        if ($slug === '')
        {
            // Fallback: keep old behavior
            $product['gallery_items'] = [];
            return $product;
        }

        $projectRoot = dirname(__DIR__, 2); // src/Models -> project root
        $publicDir   = $projectRoot . '/public';

        $dirWeb = '/assets/product-images/' . $slug;
        $dirAbs = $publicDir . $dirWeb;

        // 1) Discover originals: numeric files like 1.jpg, 2.jpg, 10.jpg
        $originals = $this->discoverOriginalGalleryImages($dirAbs, $dirWeb);

        // 2) If none found, fall back to main.jpg if present
        if (empty($originals))
        {
            $mainWeb = $dirWeb . '/main.jpg';
            if (is_file($publicDir . $mainWeb))
            {
                $originals = [$mainWeb];
            }
        }

        // Keep backwards compatible keys:
        $product['gallery'] = $originals;
        $product['image']   = $dirWeb . '/main.jpg'; // your old key; fine if missing

        // 3) Default “main” variants (hero-main/thumb-main) if they exist
        // Prefer thumb-main if it exists, otherwise use thumb-{firstOriginal}
        $heroMainWeb  = $dirWeb . '/hero-main.jpg';
        $thumbMainWeb = $dirWeb . '/thumb-main.jpg';

        // If thumb-main doesn't exist, fall back to the first discovered original (e.g. 1.jpg -> thumb-1.jpg)
        $firstOriginalWeb = $originals[0] ?? '';
        $firstName = $firstOriginalWeb !== '' ? (string) pathinfo($firstOriginalWeb, PATHINFO_FILENAME) : '';

        $thumbFallbackWeb = $firstName !== '' ? $dirWeb . '/thumb-' . $firstName . '.jpg' : '';
        $heroFallbackWeb  = $firstName !== '' ? $dirWeb . '/hero-' . $firstName . '.jpg' : '';

        $heroPick  = is_file($publicDir . $heroMainWeb)  ? $heroMainWeb  : $heroFallbackWeb;
        $listPick  = is_file($publicDir . $thumbMainWeb) ? $thumbMainWeb : $thumbFallbackWeb;

        $product['images'] = [
            'hero'  => ($heroPick !== '' && is_file($publicDir . $heroPick)) ? $this->variantSetFromPath($heroPick) : [],
            'list'  => ($listPick !== '' && is_file($publicDir . $listPick)) ? $this->variantSetFromPath($listPick) : [],
            'thumb' => ($listPick !== '' && is_file($publicDir . $listPick)) ? $this->variantSetFromPath($listPick) : [],
        ];


        // 4) Build per-image hero/thumb variants for product single
        $galleryItems = [];

        foreach ($originals as $imgWeb)
        {
            // /assets/product-images/watch/5.jpg -> name=5
            $name = (string) pathinfo($imgWeb, PATHINFO_FILENAME);

            $heroBaseWeb  = $dirWeb . '/hero-' . $name . '.jpg';
            $thumbBaseWeb = $dirWeb . '/thumb-' . $name . '.jpg';

            $galleryItems[] = [
                'hero'  => $this->variantSetFromPath($heroBaseWeb),
                'thumb' => $this->variantSetFromPath($thumbBaseWeb),
            ];
        }

        $product['gallery_items'] = $galleryItems;

        return $product;
    }

    /**
     * Finds numeric originals: 1.jpg, 2.jpg, 10.jpg ... (ignores hero-*, thumb-*, main.jpg)
     * Returns web paths sorted numerically.
     */
    private function discoverOriginalGalleryImages(string $dirAbs, string $dirWeb): array
    {
        if (!is_dir($dirAbs))
        {
            return [];
        }

        $found = [];

        foreach (glob($dirAbs . '/*.jpg') ?: [] as $abs)
        {
            $base = basename($abs);

            // Only originals like "1.jpg", "2.jpg", ...
            if (!preg_match('/^(\d+)\.jpg$/', $base, $m))
            {
                continue;
            }

            $n = (int) $m[1];
            $found[$n] = $dirWeb . '/' . $base;
        }

        ksort($found, SORT_NUMERIC);

        return array_values($found);
    }



    /**
     * Creates a predictable variant set:
     *  - jpg:  original path (or png)
     *  - jpg2: @2x path (same extension)
     *  - webp: same filename but .webp
     *  - webp2:@2x .webp
     *
     * Example:
     *  /assets/product-images/headphones/1.jpg
     *  -> jpg   /assets/product-images/headphones/1.jpg
     *  -> jpg2  /assets/product-images/headphones/1@2x.jpg
     *  -> webp  /assets/product-images/headphones/1.webp
     *  -> webp2 /assets/product-images/headphones/1@2x.webp
     */
    private function variantSetFromPath(string $path): array
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true))
        {
            return ['jpg' => $path];
        }

        // Normalize jpeg -> jpg for @2x naming consistency
        $base = preg_replace('/\.(jpe?g|png|webp)$/i', '', $path);

        $jpgExt = ($ext === 'png') ? 'png' : 'jpg';

        $jpg  = ($ext === 'webp') ? ($base . '.jpg') : $path;
        $jpg2 = $base . '@2x.' . $jpgExt;

        $webp  = $base . '.webp';
        $webp2 = $base . '@2x.webp';

        // If original was png, keep jpg pointing to png
        if ($ext === 'png')
        {
            $jpg = $base . '.png';
        }

        // If original is .jpeg, ensure jpg points to .jpg if you prefer (optional)
        if ($ext === 'jpeg')
        {
            $jpg = $base . '.jpg';
        }

        return [
            'jpg'   => $jpg,
            'jpg2'  => $jpg2,
            'webp'  => $webp,
            'webp2' => $webp2,
        ];
    }
}
