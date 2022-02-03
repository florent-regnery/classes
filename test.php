<?php
// renvoie le chemin du dossier parent
require dirname(__DIR__) . DIRECTORY_SEPARATOR. 'class' . 'user.php';
$user = new user();

var_dump($user);

$user1 = new user();
$user1->login = ?
$user1->password = ?

$user1->register($user1->login,$user1->password );
?>