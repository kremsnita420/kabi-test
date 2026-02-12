<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

final class NotFoundController extends Controller
{
    public function index(): void
    {
        echo "<h1>404 - Page Not Found</h1>";
    }
}
