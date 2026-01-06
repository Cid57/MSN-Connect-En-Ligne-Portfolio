function rechercherContacts() {
    // Récupérer la valeur du champ de recherche et la convertir en minuscules
    let rechercheValue = document.getElementById('recherche-contact').value.toLowerCase();

    // Sélectionner tous les éléments de la liste de contacts
    let contacts = document.querySelectorAll('.contact-list li');

    // Parcourir chaque contact pour vérifier s'il correspond à la recherche
    contacts.forEach(function(contact) {
        // Récupérer le nom complet du contact et le convertir en minuscules
        let contactName = contact.querySelector('.contact-details strong').textContent.toLowerCase();

        // Vérifier si le nom du contact contient la valeur de recherche
        if (contactName.includes(rechercheValue)) {
            // Si oui, afficher le contact
            contact.style.display = '';
        } else {
            // Sinon, masquer le contact
            contact.style.display = 'none';
        }
    });
}

document.getElementById('recherche-button').addEventListener('click', function() {
    rechercherContacts();
});

document.getElementById('recherche-contact').addEventListener('keypress', function(event) {
    if (event.key === 'Enter') {
        rechercherContacts();
    }
});