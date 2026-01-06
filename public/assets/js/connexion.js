// Ecoute l'événement lorsque le DOM est entièrement chargé
document.addEventListener("DOMContentLoaded", () => {
  // Initialise les icônes Feather
  if (typeof feather !== 'undefined') {
    feather.replace();
  }

  // Sélectionne toutes les icônes avec la classe "mdp-icon"
  document.querySelectorAll(".mdp-icon").forEach((icon) => {
    // Récupère l'input correspondant à l'icône
    const passwordInput = icon.previousElementSibling;
    const eye = icon.querySelector(".eye"); // Icone 'oeil' (révèle)
    const eyeOff = icon.querySelector(".eye-off"); // Icone 'œil barré' (masque)

    // Ajoute un événement au clic sur l'icône "œil" pour révéler le mot de passe
    eye.addEventListener("click", () => {
      passwordInput.type = "text"; // Affiche le mot de passe
      eye.style.display = "none"; // Masque l'icône 'œil'
      eyeOff.style.display = "block"; // Affiche l'icône 'œil barré'
    });

    // Ajoute un événement au clic sur l'icône "œil barré" pour masquer le mot de passe
    eyeOff.addEventListener("click", () => {
      passwordInput.type = "password"; // Masque le mot de passe
      eyeOff.style.display = "none"; // Masque l'icône 'œil barré'
      eye.style.display = "block"; // Affiche l'icône 'œil'
    });
  });
});




