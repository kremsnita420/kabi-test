<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

final class PageController extends Controller
{
    public function home(): void
    {
        $this->render('pages/home', [
            'title' => 'Domov – Kabi Test',
            'head'  => '<meta name="description" content="Dobrodošli na Kabi Test">',
        ]);
    }

    public function about(): void
    {
        $this->render('pages/about', [
            'title' => 'O nas – Kabi Test',
            'head'  => '<meta name="description" content="O podjetju Kabi Test">',
        ]);
    }

    public function contact(): void
    {
        $this->render('pages/contact', [
            'title' => 'Kontakt – Kabi Test',
            'head'  => '<meta name="description" content="Kontaktne informacije za Kabi Test">',
        ]);
    }

    public function writeToUs(): void
    {
        $this->render('pages/write-to-us', [
            'title' => 'Pišite nam – Kabi Test',
            'head'  => '<meta name="description" content="Pišite nam!">',
        ]);
    }
}
