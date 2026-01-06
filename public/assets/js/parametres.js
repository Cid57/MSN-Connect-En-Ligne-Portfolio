document.getElementById("avatar").addEventListener("change", function (event) {
  var reader = new FileReader();
  reader.onload = function () {
    var output = document.getElementById("avatarPreview");
    output.src = reader.result;
  };
  reader.readAsDataURL(event.target.files[0]);
});

// Gestion des icônes d'œil pour afficher/masquer le mot de passe
document.querySelectorAll(".mdp-icon").forEach(function (iconGroup) {
  var input = iconGroup.previousElementSibling;
  var eye = iconGroup.querySelector(".eye");
  var eyeOff = iconGroup.querySelector(".eye-off");

  eye.addEventListener("click", function () {
    input.type = "text";
    eye.style.display = "none";
    eyeOff.style.display = "block";
  });

  eyeOff.addEventListener("click", function () {
    input.type = "password";
    eyeOff.style.display = "none";
    eye.style.display = "block";
  });
});
