<?php
require_once 'db_config.php';
require_once 'classes/Baza.php';
require_once 'classes/User.php';
require_once 'classes/Messenger.php';

$db = new Baza(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
$user = new User($db);
$messenger = new Messenger($db);

$is_logged_in = $user->is_logged_in();
$form_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$is_logged_in) {
        $form_message = "<p class='alert-error'>Musisz być zalogowany, aby wysłać wiadomość.</p>";
    } else {
        $subject = filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_STRING);
        $content = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);
        $user_id = $user->get_user_id();

        if (empty($subject) || empty($content)) {
             $form_message = "<p class='alert-error'>Uzupełnij wszystkie pola: Temat i Treść wiadomości.</p>";
        } elseif ($messenger->startNewConversation($user_id, $subject, $content)) {
            $form_message = "<p class='alert-success'>Wiadomość wysłana! Odpowiedź otrzymasz w skrzynce 'Wiadomości' na swoim koncie.</p>";
            $subject = $content = '';
        } else {
            $form_message = "<p class='alert-error'>Wystąpił błąd podczas wysyłania wiadomości.</p>";
        }
    }
}

include 'includes/header.php';
?>

<div class="content-box">
    <header style="text-align: center; margin-bottom: 40px;">
        <h1 style="color: #0779e4; font-size: 2.5em;">📞 Skontaktuj się z nami</h1>
        <p style="font-size: 1.1em; color: white;">Jesteśmy do Twojej dyspozycji. Wybierz dogodną formę kontaktu lub odwiedź nasze biuro.</p>
    </header>
    
    <section class="contact-methods" style="display: flex; gap: 40px; margin-bottom: 40px; border-bottom: 1px dashed #ccc; padding-bottom: 30px;">
        
        <div style="flex: 1;">
            <h3 style="color: #0779e4;">Bezpośredni Kontakt</h3>
            <div style="margin-bottom: 15px;">
                <p style="font-size: 1.1em;">📧 Email: <a href="mailto:kontakt@globetrotter.pl">kontakt@globetrotter.pl</a></p>
                <p style="font-size: 1.1em;">📞 Telefon: <a href="tel:+48123456789">+48 123 456 789</a> (Dział Rezerwacji)</p>
                <p style="font-size: 1.1em;">💬 Wsparcie: +48 987 654 321 (Linia Awaryjna 24/7)</p>
            </div>

            <h3 style="color: #0779e4;">Godziny Pracy Biura</h3>
            <ul style="list-style-type: none; padding: 0;">
                <li>Poniedziałek - Piątek: 9:00 - 17:00</li>
                <li>Sobota: 10:00 - 14:00 (Tylko konsultacje telefoniczne)</li>
                <li>Niedziela: Nieczynne</li>
            </ul>
        </div>
        
        <div style="flex: 1;">
            <h3 style="color: #0779e4;">Adres i Lokalizacja</h3>
            <p style="font-size: 1.1em;">Adres: ul. Podróżnicza 10, 00-001 Warszawa</p>
            
            <div style="width: 100%; height: 200px; background-color: #f0f0f0; border: 1px solid #ccc; margin-top: 10px; overflow: hidden; border-radius: 5px;">
                <img src="images/lokalizacja_biura.jpg" alt="Statyczna mapa lokalizacji biura" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            <p style="font-size: 0.8em; margin-top: 5px; text-align: center;">Zapraszamy do odwiedzenia naszego biura!</p>
            
            <h4 style="color: #0779e4; margin-top: 20px;">Jak do nas dotrzeć?</h4>
            <p style="font-size: 0.9em;">
                 Autobus/Tramwaj: Przystanek "Plac Podróżników" (linie 123, 175, T3).<br>
                 Parking: Dostępny płatny parking przy ulicy Wyprawowej (3 minuty pieszo).
            </p>
        </div>
    </section>
    
    <section class="contact-form">
        <h2 style="text-align: center; margin-bottom: 30px; color: #333;">Wyślij Nam Wiadomość</h2>
        
        <?= $form_message ?>

        <?php if ($is_logged_in): ?>
            <p style="text-align: center; margin-bottom: 20px;">Twoja wiadomość trafi bezpośrednio do naszej skrzynki wsparcia. Odpowiedź otrzymasz na koncie w zakładce Wiadomości.</p>
            
            <form action="contact.php" method="POST" style="max-width: 500px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
                
                <div style="margin-bottom: 15px;">
                    <label for="subject" style="display: block; margin-bottom: 5px; font-weight: bold;">Temat:</label>
                    <select id="subject" name="subject" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                        <option value="" disabled selected>Wybierz temat...</option>
                        <option value="Zapytanie o rezerwację">Zapytanie o rezerwację</option>
                        <option value="Wsparcie techniczne/reklamacja">Wsparcie techniczne/reklamacja</option>
                        <option value="Inne pytanie">Inne</option>
                    </select>
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="message" style="display: block; margin-bottom: 5px; font-weight: bold;">Treść Wiadomości:</label>
                    <textarea id="message" name="message" rows="6" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; resize: vertical;"><?= htmlspecialchars($content ?? '') ?></textarea>
                </div>
                
                <input type="submit" value="Wyślij Wiadomość" style="width: 100%; padding: 12px; background-color: #0779e4; color: white; border: none; border-radius: 4px; font-size: 1.1em; cursor: pointer;">
            </form>
        
        <?php else: ?>
            <div style="text-align: center; padding: 30px; background-color: #fff3cd; border: 1px solid #ffc107; border-radius: 5px; max-width: 500px; margin: 0 auto;">
                <h4 style="color: #856404;">Aby wysłać wiadomość do biura, musisz być zalogowany.</h4>
                <p>Prosimy o <a href="login.php" style="color: #0779e4; font-weight: bold;">zalogowanie się</a> lub <a href="register.php" style="color: #0779e4; font-weight: bold;">rejestrację</a>.</p>
            </div>
        <?php endif; ?>

    </section>

</div>

<?php include 'includes/footer.php'; ?>