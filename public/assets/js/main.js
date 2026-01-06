// Cette fonction met en majuscule tous les caractères du nom
function convertToUpperCase(element) {
  let input = document.querySelector("#nom");
  input.value = input.value.toUpperCase();
}

// Cette fonction met en majuscule la première lettre du nom
function capitalizeFirstLetter() {
  let input = document.querySelector("#prenom");
  let value = input.value.toLowerCase();
  input.value = value.charAt(0).toUpperCase() + value.slice(1);
}

// Affichage de l'icon de l'oeil
document.addEventListener("DOMContentLoaded", () => {
  feather.replace();

  document.querySelectorAll(".mdp-icon").forEach((icon) => {
    const passwordInput = icon.previousElementSibling;
    const eye = icon.querySelector(".eye");
    const eyeOff = icon.querySelector(".eye-off");

    // Révèle le mot de passe
    eye.addEventListener("click", () => {
      passwordInput.type = "text";
      eye.style.display = "none";
      eyeOff.style.display = "block";
    });

    // Masque le mot de passe
    eyeOff.addEventListener("click", () => {
      passwordInput.type = "password";
      eyeOff.style.display = "none";
      eye.style.display = "block";
    });
  });
});
