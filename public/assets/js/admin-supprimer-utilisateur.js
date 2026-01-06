function rechercherUtilisateurs() {
  // Récupérer la valeur du champ de recherche et la convertir en minuscules
  let rechercheValue = document
    .getElementById("recherche-utilisateur")
    .value.toLowerCase();

  // Sélectionner tous les éléments de la liste des utilisateurs
  let utilisateurs = document.querySelectorAll(".user-checkbox");

  // Parcourir chaque utilisateur pour vérifier s'il correspond à la recherche
  utilisateurs.forEach(function (utilisateur) {
    // Récupérer le nom complet de l'utilisateur et le convertir en minuscules
    let utilisateurNomComplet = utilisateur
      .querySelector("p")
      .textContent.toLowerCase();

    // Vérifier si le nom complet de l'utilisateur contient la valeur de recherche
    if (utilisateurNomComplet.includes(rechercheValue)) {
      // Si oui, afficher l'utilisateur
      utilisateur.style.display = "";
    } else {
      // Sinon, masquer l'utilisateur
      utilisateur.style.display = "none";
    }
  });
}

//permet de valider avec la touche entree
document
  .getElementById("recherche-button")
  .addEventListener("click", function () {
    rechercherUtilisateurs();
  });

document
  .getElementById("recherche-utilisateur")
  .addEventListener("keypress", function (event) {
    if (event.key === "Enter") {
      rechercherUtilisateurs();
    }
  });
