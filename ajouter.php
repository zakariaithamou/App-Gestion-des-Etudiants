<?php
require_once 'functions.php';
exiger_connexion();

$etudiants = lire_etudiants();
$id_edit = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$mode_edition = ($id_edit > 0 && isset($etudiants[$id_edit]));


$nom     = $mode_edition ? $etudiants[$id_edit]['nom'] : '';
$prenom  = $mode_edition ? $etudiants[$id_edit]['prenom'] : '';
$filiere = $mode_edition ? $etudiants[$id_edit]['filiere'] : 'GI';
$note    = $mode_edition ? (string)$etudiants[$id_edit]['note'] : '';
$photo_actuelle = $mode_edition ? $etudiants[$id_edit]['photo'] : '';

$erreurs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom     = nettoyer($_POST['nom'] ?? '');
    $prenom  = nettoyer($_POST['prenom'] ?? '');
    $filiere = $_POST['filiere'] ?? '';
    $note_str = trim($_POST['note'] ?? '');
    $note    = $note_str;

    if ($nom === '') {
        $erreurs[] = "Le nom est obligatoire.";
    }
    if ($prenom === '') {
        $erreurs[] = "Le prénom est obligatoire.";
    }
    if (!in_array($filiere, FILIERES, true)) {
        $erreurs[] = "La filière sélectionnée est invalide.";
    }
    if ($note_str === '' || !is_numeric($note_str) || (float)$note_str < 0 || (float)$note_str > 20) {
        $erreurs[] = "La note doit être un nombre compris entre 0 et 20.";
    }

    $nouvelle_photo = $photo_actuelle;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
            $erreurs[] = "Erreur lors du téléchargement de la photo.";
        } else {
            $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
            $extensions_ok = ['jpg', 'jpeg', 'png'];
            $taille_max = 1024 * 1024;

            if (!in_array($ext, $extensions_ok, true)) {
                $erreurs[] = "Format de photo non autorisé (jpg, jpeg, png uniquement).";
            } elseif ($_FILES['photo']['size'] > $taille_max) {
                $erreurs[] = "La photo dépasse la taille maximale autorisée (1 Mo).";
            } elseif (empty($erreurs)) {
                $nom_fichier = uniqid('etu_') . '.' . $ext;
                $dossier_upload = __DIR__ . '/uploads/';

                if (!is_dir($dossier_upload)) {
                    mkdir($dossier_upload, 0777, true);
                }

                $destination = $dossier_upload . $nom_fichier;
                if (is_dir($dossier_upload) && move_uploaded_file($_FILES['photo']['tmp_name'], $destination)) {
                
                    if ($mode_edition && $photo_actuelle !== '' && file_exists($dossier_upload . $photo_actuelle)) {
                        unlink($dossier_upload . $photo_actuelle);
                    }
                    $nouvelle_photo = $nom_fichier;
                } else {
                    $erreurs[] = "Impossible d'enregistrer le fichier sur le serveur.";
                }
            }
        }
    }


    if (empty($erreurs)) {
        if ($mode_edition) {
            $id = $id_edit;
            $_SESSION['flash'] = "L'étudiant a été modifié avec succès.";
        } else {
            $id = empty($etudiants) ? 1 : (max(array_keys($etudiants)) + 1);
            $_SESSION['flash'] = "L'étudiant a été ajouté avec succès.";
        }

        $etudiants[$id] = [
            'id'      => $id,
            'nom'     => $nom,
            'prenom'  => $prenom,
            'filiere' => $filiere,
            'note'    => (float)$note_str,
            'photo'   => $nouvelle_photo
        ];

        sauver_etudiants($etudiants);
        header('Location: liste.php');
        exit;
    }
}

$titre = $mode_edition ? "Modifier un étudiant" : "Ajouter un étudiant";
include 'header.php';
?>

<div class="card form-card">
    <h2><?= h($titre) ?></h2>

    <?php if (!empty($erreurs)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($erreurs as $err): ?>
                    <li><?= h($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="ajouter.php<?= $mode_edition ? '?id=' . $id_edit : '' ?>" enctype="multipart/form-data">
        <div class="form-group">
            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom" value="<?= h($nom) ?>">
        </div>

        <div class="form-group">
            <label for="prenom">Prénom :</label>
            <input type="text" id="prenom" name="prenom" value="<?= h($prenom) ?>">
        </div>

        <div class="form-group">
            <label for="filiere">Filière :</label>
            <select id="filiere" name="filiere">
                <?php foreach (FILIERES as $f): ?>
                    <option value="<?= h($f) ?>" <?= $filiere === $f ? 'selected' : '' ?>><?= h($f) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="note">Note (/20) :</label>
            <input type="text" id="note" name="note" value="<?= h($note) ?>">
        </div>

        <div class="form-group">
            <label for="photo">Photo de profil (JPG/PNG, max 1 Mo) :</label>
            <input type="file" id="photo" name="photo" accept=".jpg,.jpeg,.png">
            <?php if ($mode_edition && $photo_actuelle !== ''): ?>
                <small>Photo actuelle : <?= h($photo_actuelle) ?></small>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary">
            <?= $mode_edition ? 'Mettre à jour' : 'Enregistrer' ?>
        </button>
        <a href="liste.php" class="btn btn-secondary">Annuler</a>
    </form>
</div>

<?php include 'footer.php'; ?>