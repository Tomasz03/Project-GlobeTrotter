<?php
require_once 'db_config.php';
require_once 'classes/Baza.php';
require_once 'classes/User.php';
$db = new Baza(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
$user = new User($db);

if ($user->is_logged_in()) { header("Location: index.php"); exit; }
$message = ''; $cls = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['password'] !== $_POST['password_confirm']) {
        $message = "Hasła różne."; $cls = 'alert-error';
    } else {
        $res = $user->register($_POST['username'], $_POST['email'], $_POST['password']);
        if ($res === true) { $message = "Konto utworzone."; $cls = 'alert-success'; }
        else { $message = $res; $cls = 'alert-error'; }
    }
}
include 'includes/header.php';
?>
<div class="content-box">
    <h2>Rejestracja</h2>
    <?php if ($message): ?><p class="<?= $cls ?>"><?= $message ?></p><?php endif; ?>
    <form action="register.php" method="POST">
        <label>Login:</label><input type="text" name="username" required>
        <label>Email:</label><input type="email" name="email" required>
        <label>Hasło:</label><input type="password" name="password" required minlength="6">
        <label>Powtórz Hasło:</label><input type="password" name="password_confirm" required>
        <input type="submit" value="Rejestracja">
    </form>
</div>
<?php include 'includes/footer.php'; ?>