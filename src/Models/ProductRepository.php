<?php

declare(strict_types=1);

namespace App\Models;

final class ProductRepository
{
    private array $products = [
        1 => [
            'id' => 1,
            'name' => 'Brezžične slušalke z aktivnim odpravljanjem šumov',
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
            'name' => 'Hitri polnilnik – prenosna baterija USB-C',
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
        return array_values($this->products);
    }

    public function find(int $id): ?array
    {
        return $this->products[$id] ?? null;
    }
}
