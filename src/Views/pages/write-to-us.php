<div class="container">
    <h1>Pišite nam</h1>

    <form method="post" action="#">
        <p>
            <label>
                Ime<br>
                <input type="text" name="name" required>
            </label>
        </p>

        <p>
            <label>
                E-pošta<br>
                <input type="email" name="email" required>
            </label>
        </p>

        <p>
            <label>
                Sporočilo<br>
                <textarea name="message" rows="6" required></textarea>
            </label>
        </p>

        <button type="submit">
            <i class="fa-solid fa-paper-plane"></i>
            Pošlji
        </button>
    </form>
</div>