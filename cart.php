<?php
require_once 'db_config.php';
require_once 'classes/Baza.php';
require_once 'classes/User.php';
require_once 'classes/Tour.php';

$db = new Baza(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
$user = new User($db);
$tour_manager = new Tour($db);

if (!$user->is_logged_in()) {
    header("Location: login.php");

    exit;
}

$message = '';
$result = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'delete_reservation') {
        $reservation_id = filter_input(INPUT_POST, 'reservation_id', FILTER_VALIDATE_INT);
        $result = $tour_manager->deleteReservation($reservation_id, $user->get_user_id());
        if ($reservation_id && $result) {
            $message = "<p class='alert-success'>Rezerwacja usunięta.</p>";
        } else {
            $message = "<p class='alert-error'>Błąd usuwania.</p>";
        }
    } elseif ($_POST['action'] === 'update_slots') {
        $reservation_id = filter_input(INPUT_POST, 'reservation_id', FILTER_VALIDATE_INT);
        $new_slots = filter_input(INPUT_POST, 'new_slots', FILTER_VALIDATE_INT);
        
        if ($reservation_id && $new_slots > 0) {
            $result = $tour_manager->updateReservationSlots($reservation_id, $new_slots, $user->get_user_id());
            if ($result) {
                $message = "<p class='alert-success'>Zaktualizowano liczbę miejsc.</p>";
            } else {
                $message = "<p class='alert-error'>Nie można zaktualizować liczby miejsc</p>";
            }
        }
    }
   
}

$reservations = $tour_manager->getUserReservations($user->get_user_id());

include 'includes/header.php';
?>

<div class="content-box">
    <h2>Mój Koszyk (Rezerwacje)</h2>
    <?= $message ?>

    <?php if (empty($reservations)): ?>
        <p>Twój koszyk jest pusty. <a href="index.php">Przeglądaj oferty.</a></p>
    <?php else: ?>
        <table style="table-layout: fixed; width: 100%;"> 
            <thead>
    <tr>
        <th style="width: 50%;">Wycieczka</th> <th style="width: 12%;">Data</th>
        <th style="width: 18%;">Miejsca</th>
        <th style="width: 10%;">Cena</th>
        <th style="width: 10%;">Suma</th>
        <th style="width: 10%;">Akcje</th>
    </tr>
</thead>
            <tbody>
                <?php $total_sum = 0; foreach ($reservations as $res): $subtotal = $res['price'] * $res['reserved_slots']; $total_sum += $subtotal; ?>
                <tr>
                    <td class="tour-cell">
                        <?php 
                        $tour_title = htmlspecialchars($res['title'] ?? 'Brak tytułu');
                        $tour_country = htmlspecialchars($res['country'] ?? '');
                        $full_title = $tour_title . ($tour_country ? ' (' . $tour_country . ')' : '');
                        ?>
                        <div class="tour-image-wrap">
                            <img src="<?= htmlspecialchars($res['image_url'] ?? 'images/placeholder.jpg') ?>" 
                                 alt="<?= $tour_title ?>">
                        </div>
                        <div class="tour-details-wrap">
                           
                                <?= $full_title ?>
                           
                        </div>
                    </td>
                    <td><?= htmlspecialchars($res['start_date']) ?></td>
                    <td>
                        <form action="cart.php" method="POST" style="display: flex; gap: 5px; justify-content: center;">
                            <input type="hidden" name="action" value="update_slots">
                            <input type="hidden" name="reservation_id" value="<?= $res['reservation_id'] ?>">
                            <input type="number" name="new_slots" value="<?= $res['reserved_slots'] ?>" min="1" style="width: 50px; text-align: center;">
                            <button type="submit" style="padding: 2px 5px;">Zmień ilość miejsc</button>
                        </form>
                    </td>
                    <td><?= number_format($res['price'], 2, ',', ' ') ?></td>
                    <td><?= number_format($subtotal, 2, ',', ' ') ?></td>
                    <td>
                        <form action="cart.php" method="POST">
                            <input type="hidden" name="action" value="delete_reservation">
                            <input type="hidden" name="reservation_id" value="<?= $res['reservation_id'] ?>">
                            <button type="submit" class="btn-danger" onclick="return confirm('Usunąć?')">Usuń</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr><td colspan="4" style="text-align: right; font-weight: bold;">Suma:</td><td style="font-weight: bold;"><?= number_format($total_sum, 2, ',', ' ') ?> PLN</td><td></td></tr>
            </tfoot>
        </table>
        
        <div style="text-align: right; margin-top: 20px;">
             <a href="https://www.mastercard.com/pl/pl.html" target="_blank" class="button">
                 Przejdź do płatności
             </a>
           </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>