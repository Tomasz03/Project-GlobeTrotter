<?php   
require_once '../db_config.php';
require_once '../classes/Baza.php';
require_once '../classes/User.php';
require_once '../classes/Tour.php';
require_once '../classes/Messenger.php'; 

$db = new Baza(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
$user = new User($db);
$tour_manager = new Tour($db);
$messenger = new Messenger($db); 

if (!$user->is_logged_in() || !$user->is_admin()) {
    header("Location: ../login.php");
    exit;
}

$admin_id = (int)$user->get_user_id();
$message = '';
$editing_tour = null;
$action = $_GET['action'] ?? 'tours'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_action = $_POST['action'] ?? '';
    if ($action === 'tours' && $post_action === 'add_edit') {
        $data = [
            'title' => $_POST['title'],
            'country' => $_POST['country'],
            'description' => $_POST['description'],
            'price' => $_POST['price'],
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
            'departure_time' => $_POST['departure_time'],
            'return_time' => $_POST['return_time'],
            'transport_type' => $_POST['transport_type'],
            'max_slots' => $_POST['max_slots'],
            'image_file' => $_FILES['image'] ?? ['error' => UPLOAD_ERR_NO_FILE],
            'current_image_url' => $_POST['current_image_url'] ?? null,
            'id' => $_POST['tour_id'] ?? null
        ];
        
        $result = $tour_manager->saveTour($data);
        if ($result === true || is_numeric($result)) {
            $message = "<p class='alert-success'>Pomyślnie zapisano zmiany w ofercie.</p>";
        } else {
            $message = "<p class='alert-error'>Błąd zapisu: " . (is_string($result) ? $result : "") . "</p>";
        }

    } elseif ($action === 'tours' && $post_action === 'delete') {
        $id = filter_input(INPUT_POST, 'tour_id', FILTER_VALIDATE_INT);
        if ($id && $tour_manager->deleteTour($id)) {
            $message = "<p class='alert-success'>Wycieczka została usunięta z bazy.</p>";
        }
    } elseif ($action === 'reservations' && $post_action === 'update_status') {
        $reservation_id = filter_input(INPUT_POST, 'reservation_id', FILTER_VALIDATE_INT);
        $new_status = $_POST['new_status'] ?? '';
        if ($reservation_id && $new_status) {
            if ($tour_manager->updateReservationStatus($reservation_id, $new_status) === true) {
                $message = "<p class='alert-success'>Status rezerwacji został zmieniony na: $new_status.</p>";
            }
        }
    }
}
if ($action === 'tours') {
    $edit_id = filter_input(INPUT_GET, 'edit_id', FILTER_VALIDATE_INT);
    if ($edit_id) $editing_tour = $tour_manager->getTourById($edit_id);
    $tours = $tour_manager->getTours();
}

if ($action === 'reservations') {
    $all_reservations = $tour_manager->getAllReservations();
}

include '../includes/header.php';
?>

<div class="content-box">
    <h1 style="color: #c0392b;">Panel Administracyjny</h1>
    
    <nav class="admin-nav" style="margin-bottom: 30px; border-bottom: 2px solid #c0392b; padding-bottom: 10px;">
        <a href="index.php?action=tours" style="padding: 10px; margin-right: 15px; border-bottom: 3px solid <?= $action === 'tours' ? '#c0392b' : 'transparent'; ?>; text-decoration: none; color: #333;">Wycieczki</a>
        <a href="index.php?action=reservations" style="padding: 10px; margin-right: 15px; border-bottom: 3px solid <?= $action === 'reservations' ? '#c0392b' : 'transparent'; ?>; text-decoration: none; color: #333;">Rezerwacje</a>
    </nav>
    
    <?= $message ?>

    <?php if ($action === 'tours'): ?>
        <h3><?= $editing_tour ? 'Edycja Wycieczki' : 'Dodaj Nową Wycieczkę' ?></h3>
        <form action="index.php?action=tours" method="POST" enctype="multipart/form-data" class="admin-form">
            <input type="hidden" name="action" value="add_edit">
            <input type="hidden" name="tour_id" value="<?= $editing_tour['id'] ?? '' ?>">
            <input type="hidden" name="current_image_url" value="<?= $editing_tour['image_url'] ?? '' ?>">
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div>
                    <label>Tytuł wycieczki:</label>
                    <input type="text" name="title" value="<?= htmlspecialchars($editing_tour['title'] ?? '') ?>" required>
                </div>
                <div>
                    <label>Kraj docelowy:</label>
                    <input type="text" name="country" value="<?= htmlspecialchars($editing_tour['country'] ?? '') ?>" required>
                </div>
            </div>

            <label>Opis szczegółowy:</label>
            <textarea name="description" rows="5" required><?= htmlspecialchars($editing_tour['description'] ?? '') ?></textarea>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-top: 10px;">
                <div>
                    <label>Cena (PLN):</label>
                    <input type="number" name="price" min="0" step="0.01" value="<?= $editing_tour['price'] ?? '' ?>" required>
                </div>
                <div>
                    <label>Data startu:</label>
                    <input type="date" name="start_date" value="<?= $editing_tour['start_date'] ?? '' ?>" required>
                </div>
                <div>
                    <label>Data końca:</label>
                    <input type="date" name="end_date" value="<?= $editing_tour['end_date'] ?? '' ?>" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-top: 10px;">
                <div>
                    <label>Godzina rozpoczęcia:</label>
                    <input type="time" name="departure_time" value="<?= $editing_tour['departure_time'] ?? '' ?>">
                </div>
                <div>
                    <label>Godzina powrotu:</label>
                    <input type="time" name="return_time" value="<?= $editing_tour['return_time'] ?? '' ?>">
                </div>
                <div>
                    <label>Środek transportu:</label>
                    <select name="transport_type">
                        <option value="Samolot" <?= ($editing_tour['transport_type'] ?? '') == 'Samolot' ? 'selected' : '' ?>>Samolot</option>
                        <option value="Autokar" <?= ($editing_tour['transport_type'] ?? '') == 'Autokar' ? 'selected' : '' ?>>Autokar</option>
                        <option value="Własny" <?= ($editing_tour['transport_type'] ?? '') == 'Własny' ? 'selected' : '' ?>>Własny</option>
                    </select>
                </div>
            </div>

            <div style="margin-top: 10px;">
                <label>Liczba dostępnych miejsc:</label>
                <input type="number" min="1" name="max_slots" value="<?= $editing_tour['max_slots'] ?? '' ?>" required>
            </div>

            <div style="margin-top: 10px;">
                <label>Zdjęcie główne:</label>
                <input type="file" name="image" accept="image/*">
                <?php if ($editing_tour && $editing_tour['image_url']): ?>
                    <p>Aktualne zdjęcie:</p>
                    <img src="../<?= htmlspecialchars($editing_tour['image_url']) ?>" style="width: 150px; border-radius: 8px; border: 1px solid #ddd;">
                <?php endif; ?>
            </div>
            
            <div style="margin-top: 20px;">
                <button type="submit" class="button"><?= $editing_tour ? 'Zaktualizuj wycieczkę' : 'Dodaj wycieczkę' ?></button>
                <?php if ($editing_tour): ?>
                    <a href="index.php?action=tours" class="button" style="background: #777; text-decoration: none;">Anuluj edycję</a>
                <?php endif; ?>
            </div>
        </form>

        <h3 style="margin-top: 50px;">Zarządzaj Wycieczkami</h3>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Zdjęcie</th>
                    <th>Kraj / Tytuł</th>
                    <th>Termin</th>
                    <th>Cena</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tours as $tour): ?>
                <tr>
                    <td><?= $tour['id'] ?></td>
                    <td><img src="../<?= htmlspecialchars($tour['image_url'] ?? 'images/placeholder.jpg') ?>" style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px;"></td>
                    <td><strong><?= htmlspecialchars($tour['country']) ?></strong><br><?= htmlspecialchars($tour['title']) ?></td>
                    <td><?= $tour['start_date'] ?> do <?= $tour['end_date'] ?></td>
                    <td><?= number_format($tour['price'], 2, ',', ' ') ?> PLN</td>
                    <td>
                        <a href="index.php?action=tours&edit_id=<?= $tour['id'] ?>" class="btn-edit">Edytuj</a>
                        <form action="index.php?action=tours" method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="tour_id" value="<?= $tour['id'] ?>">
                            <button type="submit" class="btn-delete" onclick="return confirm('Usunąć tę ofertę na stałe?')">Usuń</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    <?php elseif ($action === 'reservations'): ?>
        <h2>Lista Wszystkich Rezerwacji</h2>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Użytkownik</th>
                    <th>Wycieczka</th>
                    <th>Osoby</th>
                    <th>Data rezerwacji</th>
                    <th>Status</th>
                    <th>Akcja</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($all_reservations as $res): ?>
                <tr>
                    <td>#<?= $res['reservation_id'] ?></td>
                    <td><?= htmlspecialchars($res['username']) ?></td>
                    <td><?= htmlspecialchars($res['title']) ?></td>
                    <td><?= $res['reserved_slots'] ?></td>
                    <td><?= date('d.m.Y H:i', strtotime($res['reservation_date'])) ?></td>
                    <td style="font-weight: bold; color: <?= $res['status'] === 'paid' ? '#27ae60' : ($res['status'] === 'cancelled' ? '#c0392b' : '#f39c12'); ?>;">
                        <?= strtoupper($res['status']) ?>
                    </td>
                    <td>
                        <form action="index.php?action=reservations" method="POST" style="display: flex; gap: 5px;">
                            <input type="hidden" name="action" value="update_status">
                            <input type="hidden" name="reservation_id" value="<?= $res['reservation_id'] ?>">
                            <select name="new_status">
                                <option value="pending" <?= $res['status'] === 'pending' ? 'selected' : ''; ?>>Oczekuje</option>
                                <option value="paid" <?= $res['status'] === 'paid' ? 'selected' : ''; ?>>Opłacone</option>
                                <option value="cancelled" <?= $res['status'] === 'cancelled' ? 'selected' : ''; ?>>Anulowane</option>
                            </select>
                            <button type="submit" style="padding: 2px 8px;">OK</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>