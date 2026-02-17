<?php

declare(strict_types=1);

use App\Support\I18n;
?>

<div class="lang-switch">
    <!-- <label for="lang-select" class="sr-only">
        <?= I18n::et('lang.label') ?>
    </label> -->

    <select
        id="lang-select"
        class="lang-switch__select"
        onchange="if (this.value) window.location.href = this.value;"
        aria-label="<?= I18n::et('lang.label') ?>">
        <?php foreach ($langs as $lang): ?>
            <?php
            $href = $url->switchTo($lang);
            $selected = ($lang === $currentLang);
            ?>
            <option
                value="<?= htmlspecialchars($href, ENT_QUOTES, 'UTF-8') ?>"
                <?= $selected ? 'selected' : '' ?>>
                <?= I18n::etFor($lang, 'lang.flag') ?> <?= I18n::etFor($lang, 'lang.name') ?>
            </option>
        <?php endforeach; ?>

    </select>
</div>