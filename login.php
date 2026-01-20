<?php
require_once 'db_config.php';
require_once 'classes/Baza.php';
require_once 'classes/User.php';
$db = new Baza(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
$user = new User($db);

if ($user->is_logged_in()) { header("Location: index.php"); exit; }
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $res = $user->login($_POST['username'], $_POST['password']);
    if ($res === true) { header("Location: index.php"); exit; }
    else { $message = $res; }
}
include 'includes/header.php';
?>
<div class="content-box">
    <h2>Logowanie</h2>
    <?php if ($message): ?><p class="alert-error"><?= $message ?></p><?php endif; ?>
    <form action="login.php" method="POST">
        <label>Login:</label><input type="text" name="username" required>
        <label>Hasło:</label><input type="password" name="password" required>
        <input type="submit" value="Zaloguj">
    </form>
    <p>Nie masz konta? <a href="register.php">Zarejestruj się</a>.</p>
</div>
<?php include 'includes/footer.php'; ?>