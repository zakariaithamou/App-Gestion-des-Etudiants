<?php
session_start();


define('FICHIER', __DIR__ . '/data/etudiants.txt');
define('FILIERES', ['GI', 'IDIA']);
define('UTILISATEURS', [
    'admin' => '1234',
    'prof'  => 'ensa2026'
]);

function h(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function connecte(): bool {
    return isset($_SESSION['user']);
}

function exiger_connexion(): void {
    if (!connecte()) {
        header('Location: index.php');
        exit;
    }
}


function nettoyer(string $s): string {
    $s = trim($s);
    $s = str_replace([';', "\r", "\n"], ['', ' ', ' '], $s);
    return $s;
}

function lire_etudiants(): array {
    if (!file_exists(FICHIER)) {
        return [];
    }
    $lignes = file(FICHIER, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $etudiants = [];
    foreach ($lignes as $ligne) {
        $parts = explode(';', $ligne);
        if (count($parts) >= 6) {
            $id = (int)$parts[0];
            $etudiants[$id] = [
                'id'      => $id,
                'nom'     => $parts[1],
                'prenom'  => $parts[2],
                'filiere' => $parts[3],
                'note'    => (float)$parts[4],
                'photo'   => $parts[5]
            ];
        }
    }
    return $etudiants;
}

function sauver_etudiants(array $liste): void {
    $lignes = [];
    foreach ($liste as $etu) {
        $lignes[] = implode(';', [
            $etu['id'],
            $etu['nom'],
            $etu['prenom'],
            $etu['filiere'],
            $etu['note'],
            $etu['photo']
        ]);
    }
    $contenu = empty($lignes) ? '' : implode(PHP_EOL, $lignes) . PHP_EOL;
    file_put_contents(FICHIER, $contenu);
}