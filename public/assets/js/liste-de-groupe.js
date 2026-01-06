function rechercherGroupes() {
    // Récupérer la valeur du champ de recherche et la convertir en minuscules
    let rechercheValue = document.getElementById('recherche-groupe').value.toLowerCase();

    // Sélectionner tous les éléments de la liste de groupes
    let groupes = document.querySelectorAll('.contact-list li');

    // Parcourir chaque groupe pour vérifier s'il correspond à la recherche
    groupes.forEach(function(groupe) {
        // Récupérer le nom du groupe et le convertir en minuscules
        var groupeName = groupe.querySelector('.contact-details strong').textContent.toLowerCase();

        // Vérifier si le nom du groupe contient la valeur de recherche
        if (groupeName.includes(rechercheValue)) {
            // Si oui, afficher le groupe
            groupe.style.display = '';
        } else {
            // Sinon, masquer le groupe
            groupe.style.display = 'none';
        }
    });
}

document.getElementById('recherche-button').addEventListener('click', function() {
    rechercherGroupes();
});

document.getElementById('recherche-groupe').addEventListener('keypress', function(event) {
    if (event.key === 'Enter') {
        rechercherGroupes();
    }
});
