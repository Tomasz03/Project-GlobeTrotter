<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'db_config.php';
require_once 'classes/Baza.php';
require_once 'classes/User.php';
require_once 'classes/Tour.php';

$db = new Baza(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
$user = new User($db);
$tour_manager = new Tour($db);

$message = '';
$tour_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);


$tour = $tour_manager->getTourById($tour_id);

if (!$tour) {
    include 'includes/header.php';
    echo "<div class='content-box'><h2>Błąd</h2><p class='alert-error'>Wycieczka o podanym ID nie istnieje.</p></div>";
    include 'includes/footer.php';
    exit;
}


$available_slots = $tour_manager->getAvailableSlots($tour_id);



if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user->is_logged_in() && $tour_id) {
    $slots = filter_input(INPUT_POST, 'slots', FILTER_VALIDATE_INT);

    if ($slots <= 0) {
        $_SESSION['error'] = "Liczba miejsc musi być dodatnia.";
    } elseif ($slots > $available_slots) {
        $_SESSION['error'] = "Wybrano za dużo miejsc. Dostępnych jest tylko $available_slots.";
    } else {
        $result = $tour_manager->addReservation($user->get_user_id(), $tour_id, $slots);
        
     
        if ($result === true || $result === 1) { 
            $_SESSION['success'] = "Wycieczka pomyślnie dodana do koszyka!";
        } elseif (is_string($result)) {
            $_SESSION['error'] = $result;
        } else {
            $_SESSION['error'] = "Błąd podczas dodawania rezerwacji.";
        }
    }
    
 
    header("Location: tour_details.php?id=" . $tour_id);
    exit;
}



if (isset($_SESSION['success'])) {
    $message .= "<p class='alert-success'>" . htmlspecialchars($_SESSION['success']) . "</p>";
    unset($_SESSION['success']);
}
if (isset($_SESSION['error'])) {
    $message .= "<p class='alert-error'>" . htmlspecialchars($_SESSION['error']) . "</p>";
    unset($_SESSION['error']);
}


include 'includes/header.php';
?>

<div class="content-box">
    <a href="index.php"><input type="submit" value="Powrót do listy"></a>
    <h2>Szczegóły Wycieczki: <?= htmlspecialchars($tour['title']) ?> (<?= htmlspecialchars($tour['country']) ?>)</h2>

    <?= $message ?>
    
    <div style="text-align: center; margin-bottom: 20px;">
        <img src="<?= htmlspecialchars($tour['image_url'] ?: 'images/placeholder.jpg') ?>" 
             alt="Zdjęcie wycieczki" 
             style="max-width: 100%; height: auto; border-radius: 5px;">
    </div>

    <div>
        <h3>Podstawowe Informacje</h3>
        <p><strong>Cena:</strong> <span style="font-size: 1.2em; color: #0779e4; font-weight: bold;"><?= number_format($tour['price'], 2, ',', ' ') ?> PLN</span></p>
        <p><strong>Długość:</strong> <?= htmlspecialchars($tour['duration_days']) ?> dni</p>
        <p><strong>Wolne miejsca:</strong> <span style="font-weight: bold; color: <?= $available_slots > 0 ? 'green' : 'red' ?>;"><?= $available_slots ?></span> / <?= htmlspecialchars($tour['max_slots']) ?></p>
        <p><strong>Transport:</strong> <?= htmlspecialchars($tour['transport_type']) ?></p>

        <h3>Plan Podróży</h3>
        <p><strong>Wylot/Wyjazd:</strong> <?= htmlspecialchars($tour['start_date']) ?> o godzinie <?= htmlspecialchars($tour['departure_time']) ?></p>
        <p><strong>Powrót:</strong> <?= htmlspecialchars($tour['end_date']) ?> o godzinie  <?= htmlspecialchars($tour['return_time']) ?></p>
        
        <h3>Opis Wycieczki</h3>
        <p><?= nl2br(htmlspecialchars($tour['description'])) ?></p>
    </div>

    <?php if ($user->is_logged_in()): ?>
        <h3>Dodaj do Koszyka (Rezerwuj)</h3>
        <?php if ($available_slots > 0): ?>
            <form action="tour_details.php?id=<?= $tour['id'] ?>" method="POST">
                <label for="slots">Liczba miejsc (1-<?= $available_slots ?>):</label>
                <input type="number" id="slots" name="slots" min="1" max="<?= $available_slots ?>" value="1" required>
                <input type="submit" value="Rezerwuj i dodaj do koszyka">
            </form>
        <?php else: ?>
             <p class='alert-error'>Brak wolnych miejsc na tę wycieczkę.</p>
        <?php endif; ?>
    <?php else: ?>
        <p class='alert-error'>Musisz się <a href="login.php">zalogować</a>, aby dokonać rezerwacji.</p>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>