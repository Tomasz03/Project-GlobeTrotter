<?php
require_once 'db_config.php';
require_once 'classes/Baza.php';
require_once 'classes/User.php';
require_once 'classes/Messenger.php'; 
require_once 'classes/Tour.php';     

$db = new Baza(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
$user = new User($db);
$messenger = new Messenger($db);
$tour_manager = new Tour($db);

if (!$user->is_logged_in()) {
    header("Location: login.php");
    ob_end_flush();
    exit;
}

$userData = $user->getUserData($user->get_user_id());
$user_id = $user->get_user_id();
$message = '';
$action = $_GET['action'] ?? 'details'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'details') {
    $old_pass = $_POST['old_password'];
    $new_pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    if ($new_pass !== $confirm_pass) {
        $message = "<p class='alert-error'>Nowe hasła nie są identyczne.</p>";
    } else {
        $result = $user->changePassword($user_id, $old_pass, $new_pass);
        if ($result === true) {
            $message = "<p class='alert-success'>Hasło zostało zmienione.</p>";
        } else {
            $message = "<p class='alert-error'>$result</p>";
        }
    }
}


$current_conversation_id = filter_input(INPUT_GET, 'conv', FILTER_VALIDATE_INT);
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'messages' && isset($_POST['send_message']) && $current_conversation_id) {
    $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_STRING);
    if (!empty($content)) {
        if ($messenger->sendMessage($current_conversation_id, $user_id, $content)) {
             header("Location: account.php?action=messages&conv=" . $current_conversation_id);
             exit;
        } else {
             $message = "<p class='alert-error'>Błąd wysyłania wiadomości.</p>";
        }
    }
}


include 'includes/header.php';
?>

<div class="content-box">
    <h2>Mój Panel Użytkownika</h2>
    
    <nav class="account-nav" style="margin-bottom: 30px; border-bottom: 2px solid #ddd; padding-bottom: 10px;">
        <a href="account.php?action=details" style="padding: 10px; margin-right: 15px; border-bottom: 3px solid <?= $action === 'details' ? '#0779e4' : 'transparent'; ?>; text-decoration: none; color: #333;">Moje Dane</a>
        <a href="account.php?action=messages" style="padding: 10px; margin-right: 15px; border-bottom: 3px solid <?= $action === 'messages' ? '#0779e4' : 'transparent'; ?>; text-decoration: none; color: #333;">Wiadomości</a>
        <a href="account.php?action=history" style="padding: 10px; border-bottom: 3px solid <?= $action === 'history' ? '#0779e4' : 'transparent'; ?>; text-decoration: none; color: #333;">Historia Wycieczek</a>
    </nav>
    
    <?= $message ?>

    <?php if ($action === 'details'): ?>
        <div style="margin-bottom: 30px;">
            <h3>Twoje Dane</h3>
            <p><strong>Użytkownik:</strong> <?= htmlspecialchars($userData['username']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($userData['email']) ?></p>
            <p><strong>Data rejestracji:</strong> <?= htmlspecialchars($userData['created_at']) ?></p>
        </div>

        <h3>Zmiana Hasła</h3>
        <form action="account.php?action=details" method="POST">
            <label>Stare Hasło:</label>
            <input type="password" name="old_password" required>
            <label>Nowe Hasło (min. 6 znaków):</label>
            <input type="password" name="new_password" required minlength="6">
            <label>Potwierdź Nowe Hasło:</label>
            <input type="password" name="confirm_password" required minlength="6">
            <input type="submit" value="Zmień Hasło">
        </form>

    <?php elseif ($action === 'messages'): ?>
        <?php 
            
            include 'includes/account_messages.php'; 
        ?>

   <?php elseif ($action === 'history'): ?>
    <h3>Historia Opłaconych Wycieczek</h3>
    <?php $paid_reservations = $tour_manager->getUserPaidReservations($user_id); ?>
    
    <?php if (empty($paid_reservations)): ?>
        <p>Brak opłaconych wycieczek w Twojej historii.</p>
    <?php else: ?>
        <table style="width: 100%;">
            <thead>
                <tr>
                    <th style="width: 40%;">Wycieczka</th> <th>Data Rezerwacji</th>
                    <th>Termin</th>
                    <th>Miejsca</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($paid_reservations as $res): ?>
                    <tr>
                        <td class="tour-cell">
                            <div class="tour-image-wrap">
                                <img src="<?= htmlspecialchars($res['image_url'] ?: 'images/placeholder.jpg') ?>" 
                                     alt="<?= htmlspecialchars($res['title']) ?>">
                            </div>
                            <div class="tour-details-wrap">
                                <a href="tour_details.php?id=<?= $res['id'] ?>">
                                    <?= htmlspecialchars($res['title']) ?>
                                </a>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($res['reservation_date']) ?></td>
                        <td><?= htmlspecialchars($res['start_date']) ?> do <?= htmlspecialchars($res['end_date']) ?></td>
                        <td><?= $res['reserved_slots'] ?></td>
                        <td style="color: green; font-weight: bold;">OPŁACONE</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
<?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
