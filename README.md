# Gestion des Étudiants (PHP)

Application web développée en PHP natif permettant de gérer les étudiants de 2ème année du cycle ingénieur (**GINF** / **GIA**) à l'ENSAS : authentification, gestion des notes par semestre (S3 et S4), calcul de la moyenne annuelle et décision d'admission.

---

## Fonctionnalités

- **Authentification & Sécurité :**
  - Connexion via formulaire `POST` avec gestion de session (`$_SESSION`).
  - Mémorisation du dernier identifiant via un cookie valable 7 jours (`$_COOKIE`).
  - Protection des pages privées et échappement des sorties HTML (`htmlspecialchars`).
- **Gestion des Étudiants (CRUD) :**
  - **Ajout et Modification :** Saisie des informations personnelles, choix de la filière (`GINF` ou `GIA`), et saisie des notes des **7 modules du Semestre 3** et des **7 modules du Semestre 4**.
  - **Photo de profil :** Téléchargement sécurisé (`JPG`, `JPEG`, `PNG`, max 1 Mo) avec renommage unique (`uniqid()`) et suppression automatique de l'ancienne image lors d'une modification ou suppression.
  - **Stockage sans base de données :** Persistance des données dans un fichier texte structuré (`data/etudiants.txt`).
- **Suivi Académique & Statistiques :**
  - Calcul automatique des moyennes **S3**, **S4** et de la **Moyenne Annuelle**.
  - Attribution automatique du statut **Admis** (>= 12) ou **Non admis** (< 12).
  - Barre de statistiques globales : effectif affiché, moyenne de la promotion et meilleure moyenne.
- **Recherche & Tri :**
  - Filtrage côté serveur (`GET`) par nom/prénom et tri par ordre d'ajout, nom, filière ou moyenne annuelle décroissante.
  - Recherche instantanée côté client en JavaScript sans rechargement de page.

---

## Structure du Projet

```text
gestion_etudiants/
├── data/
│   └── etudiants.txt      # Stockage des étudiants et de leurs notes
├── uploads/               # Dossier des photos de profil téléchargées
├── images/                # Ressources graphiques (logo ENSA)
├── index.php              # Page de connexion
├── liste.php              # Tableau des résultats, recherche, tri et statistiques
├── ajouter.php            # Formulaire d'ajout et de modification d'un étudiant
├── supprimer.php          # Script de suppression d'un étudiant et de sa photo
├── logout.php             # Déconnexion et destruction de session
├── functions.php          # Constantes, sessions, lecture/écriture fichier et calculs
├── header.php             # En-tête HTML et barre de navigation
├── footer.php             # Pied de page et inclusion du script JS
├── style.css              # Feuille de style CSS
├── script.js              # Confirmation de suppression et filtre instantané
└── test.php               # Vérification de la configuration PHP (phpinfo)
```

---

## Installation et Exécution

1. **Cloner le dépôt** dans le répertoire racine de votre serveur local (`htdocs` pour XAMPP, `www` pour WAMP/Laragon) :
   ```bash
   git clone <url-de-votre-depot> gestion_etudiants
   cd gestion_etudiants
   ```

2. **Démarrer le serveur PHP** (via XAMPP Apache ou le serveur intégré de PHP) :
   ```bash
   php -S localhost:8000
   ```

3. **Accéder à l'application** dans votre navigateur :
   ```text
   http://localhost:8000/index.php
   ```

---

## Identifiants de Connexion (Test)

| Rôle | Login | Mot de passe |
| :--- | :--- | :--- |
| Administrateur | `admin` | `1234` |
| Enseignant | `prof` | `ensa2026` |
