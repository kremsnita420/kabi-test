<?php
declare(strict_types=1);

use App\Support\I18n;
?>

<div class="container">
    <h1><?= I18n::et('write.title') ?></h1>

    <form method="post" action="#">
        <p>
            <label>
                <?= I18n::et('form.name') ?><br>
                <input type="text" name="name" required>
            </label>
        </p>

        <p>
            <label>
                <?= I18n::et('form.email') ?><br>
                <input type="email" name="email" required>
            </label>
        </p>

        <p>
            <label>
                <?= I18n::et('form.message') ?><br>
                <textarea name="message" rows="6" required></textarea>
            </label>
        </p>

        <button type="submit">
            <i class="fa-solid fa-paper-plane"></i>
            <?= I18n::et('form.send') ?>
        </button>
    </form>
</div>