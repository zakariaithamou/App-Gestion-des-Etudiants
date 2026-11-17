<?php
require_once 'functions.php';
exiger_connexion();

$etudiants = lire_etudiants();

// Récupération des paramètres GET de recherche et de tri (Exercice 7)
$q   = trim($_GET['q'] ?? '');
$tri = $_GET['tri'] ?? 'id';
$tris_valides = ['id', 'nom', 'filiere', 'note'];
if (!in_array($tri, $tris_valides, true)) {
    $tri = 'id';
}

// Filtrage avec array_filter et stripos
if ($q !== '') {
    $etudiants = array_filter($etudiants, function ($etu) use ($q) {
        return stripos($etu['nom'], $q) !== false || stripos($etu['prenom'], $q) !== false;
    });
}

// Tri avec usort
if ($tri === 'nom') {
    usort($etudiants, fn($a, $b) => strcasecmp($a['nom'], $b['nom']));
} elseif ($tri === 'filiere') {
    usort($etudiants, fn($a, $b) => strcasecmp($a['filiere'], $b['filiere']));
} elseif ($tri === 'note') {
    usort($etudiants, fn($a, $b) => $b['note'] <=> $a['note']); // Décroissant
}

// Calcul des statistiques sans division par zéro (Exercice 7)
$nb = count($etudiants);
$moyenne = 0;
$meilleure = 0;
if ($nb > 0) {
    $notes = array_column($etudiants, 'note');
    $moyenne = array_sum($notes) / $nb;
    $meilleure = max($notes);
}

$titre = "Liste des étudiants";
include 'header.php';
?>

<?php if (isset($_SESSION['flash'])): ?>
    <div class="alert alert-success"><?= h($_SESSION['flash']) ?></div>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<div class="stats-bar">
    <span><strong>Étudiants affichés :</strong> <?= $nb ?></span>
    <span><strong>Moyenne :</strong> <?= number_format($moyenne, 2) ?> / 20</span>
    <span><strong>Meilleure note :</strong> <?= number_format($meilleure, 2) ?> / 20</span>
</div>

<div class="card filter-card">
    <form method="get" action="liste.php" class="filter-form">
        <input type="text" id="search-input" name="q" placeholder="Rechercher par nom ou prénom..." value="<?= h($q) ?>">
        <select name="tri">
            <option value="id" <?= $tri === 'id' ? 'selected' : '' ?>>Ordre d'ajout</option>
            <option value="nom" <?= $tri === 'nom' ? 'selected' : '' ?>>Nom (A-Z)</option>
            <option value="filiere" <?= $tri === 'filiere' ? 'selected' : '' ?>>Filière</option>
            <option value="note" <?= $tri === 'note' ? 'selected' : '' ?>>Note (décroissante)</option>
        </select>
        <button type="submit" class="btn btn-primary">Filtrer</button>
        <a href="liste.php" class="btn btn-secondary">Réinitialiser</a>
    </form>
</div>

<div class="card">
    <table id="table-etudiants">
        <thead>
            <tr>
                <th>Photo</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Filière</th>
                <th>Note</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($nb === 0): ?>
                <tr>
                    <td colspan="6" class="text-center">Aucun étudiant</td>
                </tr>
            <?php else: ?>
                <?php foreach ($etudiants as $etu): ?>
                    <?php
                        $classe_note = ($etu['note'] >= 10) ? 'note-verte' : 'note-rouge';
                        $nom_photo = basename((string)$etu['photo']);
                        $chemin_photo = ($nom_photo !== '' && file_exists(__DIR__ . '/uploads/' . $nom_photo))
                            ? 'uploads/' . rawurlencode($nom_photo)
                            : 'https://via.placeholder.com/45?text=Etu';
                    ?>
                    <tr class="ligne-etudiant">
                        <td><img src="<?= $chemin_photo ?>" alt="Photo" class="avatar"></td>
                        <td class="col-nom"><?= h(strtoupper($etu['nom'])) ?></td>
                        <td class="col-prenom"><?= h(ucfirst(strtolower($etu['prenom']))) ?></td>
                        <td><?= h($etu['filiere']) ?></td>
                        <td class="<?= $classe_note ?>"><?= number_format($etu['note'], 2) ?></td>
                        <td>
                            <a href="ajouter.php?id=<?= (int)$etu['id'] ?>" class="btn-action btn-edit">Modifier</a>
                            <a href="supprimer.php?id=<?= (int)$etu['id'] ?>" class="btn-action btn-delete">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'footer.php'; ?>