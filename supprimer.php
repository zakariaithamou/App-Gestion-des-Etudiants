<?php
require_once 'functions.php';
exiger_connexion();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$etudiants = lire_etudiants();

if ($id > 0 && isset($etudiants[$id])) {
    $photo = $etudiants[$id]['photo'];
    if ($photo !== '' && file_exists(__DIR__ . '/uploads/' . $photo)) {
        unlink(__DIR__ . '/uploads/' . $photo);
    }

    unset($etudiants[$id]);
    sauver_etudiants($etudiants);
    $_SESSION['flash'] = "L'étudiant a été supprimé.";
}

header('Location: liste.php');
exit;