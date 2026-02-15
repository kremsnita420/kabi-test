<?php

declare(strict_types=1);

$e = static fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$products = $products ?? [];
?>

<section class="admin-upload">
    <div class="admin-upload__header">
        <h1>Upload slik</h1>
        <p>Najprej izberi izdelek, nato naloži sliko. Sistem samodejno ustvari <strong>hero</strong>, <strong>thumb</strong>, <strong>@2x</strong> in <strong>WebP</strong>.</p>
    </div>

    <div class="admin-upload__controls">
        <label class="admin-upload__label" for="productSelect">Izberi izdelek</label>

        <div class="admin-upload__select-wrap">
            <input
                id="productSearch"
                class="admin-upload__search"
                type="search"
                placeholder="Išči izdelek…"
                autocomplete="off">

            <select id="productSelect" class="admin-upload__select" size="6" aria-label="Seznam izdelkov">
                <?php foreach ($products as $p): ?>
                    <option value="<?= $e($p['slug']) ?>">
                        <?= $e($p['name']) ?> (<?= $e($p['slug']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="admin-upload__target" id="uploadTarget">
            Izbran izdelek: <strong class="admin-upload__target-name">—</strong>
            <span class="admin-upload__target-path"></span>
        </div>
    </div>

    <div class="admin-upload__grid">
        <div
            class="dropzone is-disabled"
            id="dropzone"
            data-endpoint-base="/admin/upload">
            <div class="dropzone__inner">
                <i class="fa-solid fa-cloud-arrow-up"></i>
                <p><strong>Povleci sliko</strong> sem ali klikni za izbor.</p>
                <small id="dropzoneHint">Najprej izberi izdelek.</small>
            </div>

            <input type="file" id="fileInput" accept="image/jpeg,image/png" hidden>
        </div>

        <div class="upload-result" id="uploadResult" aria-live="polite">
            <div class="upload-result__empty">
                <p>Tu se prikaže rezultat (hero/thumb) po uspešnem uploadu.</p>
            </div>
        </div>
    </div>
</section>