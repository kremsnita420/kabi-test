<?php
declare(strict_types=1);

use App\Support\I18n;
?>

<div class="container">
    <h1><?= I18n::et('contact.title') ?></h1>

    <ul>
        <li><strong><?= I18n::et('contact.email') ?>:</strong> info@example.com</li>
        <li><strong><?= I18n::et('contact.phone') ?>:</strong> +386 40 000 000</li>
        <li><strong><?= I18n::et('contact.address') ?>:</strong> Ljubljana, Slovenija</li>
    </ul>
</div>