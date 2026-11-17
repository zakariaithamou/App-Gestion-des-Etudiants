document.addEventListener("DOMContentLoaded", () => {
  // 1. Confirmation avant suppression
  const liensSuppression = document.querySelectorAll(".btn-delete");
  liensSuppression.forEach((lien) => {
    lien.addEventListener("click", (e) => {
      if (!confirm("Voulez-vous vraiment supprimer cet étudiant ?")) {
        e.preventDefault();
      }
    });
  });

  // 2. Recherche instantanée dans le tableau
  const champRecherche = document.getElementById("search-input");
  const lignes = document.querySelectorAll(
    "#table-etudiants tbody tr.ligne-etudiant",
  );

  if (champRecherche && lignes.length > 0) {
    champRecherche.addEventListener("input", () => {
      const filtre = champRecherche.value.toLowerCase().trim();
      lignes.forEach((ligne) => {
        const nom = ligne.querySelector(".col-nom").textContent.toLowerCase();
        const prenom = ligne
          .querySelector(".col-prenom")
          .textContent.toLowerCase();
        if (nom.includes(filtre) || prenom.includes(filtre)) {
          ligne.style.display = "";
        } else {
          ligne.style.display = "none";
        }
      });
    });
  }
});
