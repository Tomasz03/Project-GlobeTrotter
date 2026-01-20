<?php
require_once 'db_config.php';
require_once 'classes/Baza.php';
require_once 'classes/User.php';

$db = new Baza(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
$user = new User($db);

$user->logout(); 
?>