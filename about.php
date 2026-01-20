<?php

require_once 'db_config.php';
require_once 'classes/Baza.php';
require_once 'classes/User.php';

$db = new Baza(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
$user = new User($db);

include 'includes/header.php';
?>

<div class="content-box">
    <header style="text-align: center; margin-bottom: 40px;">
        <h1 style="color: #0779e4; font-size: 2.5em;">🌍 Odkrywaj Świat z Globetrotter</h1>
        <p style="font-size: 1.2em; color: white;">Twoja brama do niezapomnianych przygód. Od 10 lat spełniamy marzenia o podróżach.</p>
    </header>

    <section class="mission" style="margin-bottom: 40px;">
        <h2 style="border-bottom: 2px solid #ccc; padding-bottom: 10px; margin-bottom: 20px;">Nasza Misja i Wartości</h2>
        <div style="display: flex; gap: 30px; align-items: flex-start;">
            <div style="flex: 2;">
                <p style="line-height: 1.6;">
                    W Globetrotter wierzymy, że podróżowanie to coś więcej niż przemieszczanie się. To inwestycja we wspomnienia,
                    wiedzę o świecie i rozwój osobisty. Naszą misją jest dostarczanie niezapomnianych wrażeń
                    i bezpiecznych podróży w najlepszych cenach, z dbałością o każdy detal.
                </p>
                <p>Od egzotycznych wypraw do Azji, przez historyczne szlaki Ameryki Południowej, aż po urokliwe weekendowe wypady
                    do stolic Europy  projektujemy podróże, które zostają z Tobą na zawsze.</p>
            </div>
            <div style="flex: 1; background: #f4f4f4; padding: 15px; border-left: 4px solid #0779e4; border-radius: 5px;">
                <h4 style="color: #0779e4; margin-top: 0;">Nasze Filary:</h4>
                <ul style="list-style-type: none; padding: 0;">
                    <li>Bezpieczeństwo (Pełne ubezpieczenie i wsparcie)</li>
                    <li>Jakość(Starannie dobrane hotele i przewodnicy)</li>
                    <li>Pasja (Projektowanie tras przez doświadczonych podróżników)</li>
                </ul>
            </div>
        </div>
    </section>

    <section class="why-us" style="margin-bottom: 40px;">
        <h2 style="border-bottom: 2px solid #ccc; padding-bottom: 10px; margin-bottom: 20px;">Dlaczego Klienci Nam Ufają?</h2>
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; text-align: center;">
            
            <div style="padding: 15px; border: 1px solid #eee; border-radius: 5px;">
                <span style="font-size: 2.5em; color: #0779e4;">⭐</span>
                <h4 style="margin-top: 5px;">10 Lat Doświadczenia</h4>
                <p style="font-size: 0.9em;">Jesteśmy na rynku od dekady, co gwarantuje stabilność i wiedzę ekspercką.</p>
            </div>

            <div style="padding: 15px; border: 1px solid #eee; border-radius: 5px;">
                <span style="font-size: 2.5em; color: #0779e4;">🔒</span>
                <h4 style="margin-top: 5px;">Gwarancja Ceny</h4>
                <p style="font-size: 0.9em;">Oferujemy konkurencyjne pakiety bez ukrytych opłat.</p>
            </div>

            <div style="padding: 15px; border: 1px solid #eee; border-radius: 5px;">
                <span style="font-size: 2.5em; color: #0779e4;">🗺️</span>
                <h4 style="margin-top: 5px;">Specjalizacja Egzotyczna</h4>
                <p style="font-size: 0.9em;">Jesteśmy ekspertami od destynacji, o których inni tylko marzą.</p>
            </div>
        </div>
    </section>
    
    <section class="contact-info">
        <h2 style="border-bottom: 2px solid #ccc; padding-bottom: 10px; margin-bottom: 20px;">Kontakt i Dane Firmowe</h2>
        
        <div style="display: flex; gap: 40px;">
            <div style="flex: 1;">
                <h4 style="color: #0779e4;">Dane Rejestrowe</h4>
                <p>
                    <strong>Globetrotter Sp. z o.o.</strong><br>
                    ul. Podróżnicza 15/4<br>
                    00-999 Warszawa, Polska
                </p>
                <p>
                    <strong>NIP:</strong> 123-456-78-90<br>
                    <strong>REGON:</strong> 123456789
                </p>
            </div>
            
            <div style="flex: 1;">
                <h4 style="color: #0779e4;">Biuro Obsługi</h4>
                <p>
                    📞 Telefon: +48 123 456 789<br>
                    📧 Email: kontakt@globetrotter.pl<br>
                    🕒 Czynne: Pon.-Pt., 9:00 - 17:00
                </p>
                <p style="margin-top: 15px; background: #e6f7ff; padding: 10px; border-radius: 3px;">
                    Masz pytania? <a href="contact.php" style="color: #0779e4; font-weight: bold;">Skontaktuj się z nami!</a>
                </p>
            </div>
        </div>

    </section>

</div>

<?php include 'includes/footer.php'; ?>