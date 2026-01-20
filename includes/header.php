<?php
if (!isset($db) || !isset($user)) {
    die("Błąd inicjalizacji aplikacji."); 
}
$base_path = (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? '../' : '';
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Globetrotter - Biuro Podróży</title>
    <link rel="stylesheet" href="<?= $base_path ?>css/style.css">
</head>
<body>

<header>
    <div class="container">
        <div id="branding">
            <h1><span class="current">Globetrotter</span></h1>
        </div>
        <nav>
            <ul>
                <li><a href="<?= $base_path ?>index.php">Wycieczki</a></li>
                <li><a href="<?= $base_path ?>about.php">O Nas</a></li>
                <li><a href="<?= $base_path ?>contact.php">Kontakt</a></li>
                
                <?php if ($user->is_logged_in()): ?>
                    <li class="dropdown">
                        <button class="dropbtn"><?= htmlspecialchars($_SESSION['username']) ?> ▼</button>
                        <div class="dropdown-content">
                            <a href="<?= $base_path ?>account.php">Moje Konto</a> 
                            <a href="<?= $base_path ?>cart.php">Koszyk / Rezerwacje</a> 
                            
                            <?php if ($user->is_admin()): ?>
                                <a href="<?= $base_path ?>admin/index.php" style="color: red; font-weight: bold;">Panel Admina</a>
                            <?php endif; ?>
                            <a href="<?= $base_path ?>logout.php">Wyloguj</a>
                        </div>
                    </li>
                <?php else: ?>
                    <li><a href="<?= $base_path ?>login.php">Logowanie</a></li>
                    <li><a href="<?= $base_path ?>register.php">Rejestracja</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>

<div class="container">