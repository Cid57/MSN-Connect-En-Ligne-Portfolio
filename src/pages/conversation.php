<?php
// Démarrer une session si ce n'est pas déjà fait
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vérifier si l'utilisateur est connecté
if (empty($_SESSION['id_utilisateur'])) {
    header('Location: /?page=connexion');
    exit;
}



// Récupérer l'ID de l'utilisateur connecté
$idUtilisateur = $_SESSION['id_utilisateur'];

// Vérifier si un destinataire ou un canal est spécifié dans les paramètres de la requête
$idDestinataire = isset($_GET['id_destinataire']) ? (int)$_GET['id_destinataire'] : null;
$idChannel = isset($_GET['id_channel']) ? (int)$_GET['id_channel'] : null;

if ($idDestinataire && !$idChannel && $idDestinataire != $idUtilisateur) {
    // Gestion de la conversation individuelle

    // Vérifier si le destinataire est actif
    $query = $dbh->prepare("SELECT est_actif FROM utilisateur WHERE id_utilisateur = :id_destinataire");
    $query->execute(['id_destinataire' => $idDestinataire]);
    $destinataire = $query->fetch();

    if (!$destinataire['est_actif']) {
        // Si le destinataire n'existe pas ou est inactif, rediriger avec un message d'erreur
        $_SESSION['error_message'] = "Impossible de démarrer une conversation avec un utilisateur désactivé.";
        header('Location: /?page=liste-de-contact');
        exit;
    }


    // Vérifier si une conversation existe déjà entre l'utilisateur et le destinataire
    $query = $dbh->prepare(
        "SELECT channel.id_channel FROM channel
        INNER JOIN acces AS a1 ON channel.id_channel = a1.id_channel
        INNER JOIN acces AS a2 ON channel.id_channel = a2.id_channel
        WHERE a1.id_utilisateur = :id_utilisateur AND a2.id_utilisateur = :id_destinataire AND channel.est_groupe = 0"
    );
    $query->execute([
        'id_utilisateur' => $idUtilisateur,
        'id_destinataire' => $idDestinataire
    ]);

    // Récupérer le canal de la conversation si elle existe
    $channel = $query->fetch();

    if ($channel) {
        // Si la conversation existe, récupérer l'ID du canal
        $idChannel = $channel['id_channel'];
    } else {
        // Sinon, créer une nouvelle conversation
        $dbh->beginTransaction();
        try {
            // Insérer un nouveau canal dans la table channel
            $query = $dbh->prepare("INSERT INTO channel (est_groupe, est_actif) VALUES (0, 1)");
            $query->execute();
            $idChannel = $dbh->lastInsertId();

            // Ajouter les accès pour l'utilisateur et le destinataire
            $query = $dbh->prepare("INSERT INTO acces (id_channel, id_utilisateur) VALUES (:id_channel, :id_utilisateur)");
            $query->execute(['id_channel' => $idChannel, 'id_utilisateur' => $idUtilisateur]);
            $query->execute(['id_channel' => $idChannel, 'id_utilisateur' => $idDestinataire]);

            // Valider la transaction
            $dbh->commit();
        } catch (Exception $e) {
            // En cas d'erreur, annuler la transaction
            $dbh->rollBack();
            echo "Erreur : " . $e->getMessage();
            exit;
        }
    }



    // Rediriger vers la page de conversation avec l'ID du canal
    header('Location: /?page=conversation&id_channel=' . $idChannel);
    exit;
}



if ($idChannel) {
    // Vérifier si l'utilisateur a accès au groupe ou à la conversation
    $query = $dbh->prepare("SELECT * FROM acces WHERE id_channel = :id_channel AND id_utilisateur = :id_utilisateur");
    $query->execute(['id_channel' => $idChannel, 'id_utilisateur' => $idUtilisateur]);
    $acces = $query->fetch();

    if (!$acces) {
        // Si l'utilisateur n'a pas accès au groupe ou à la conversation, rediriger vers la page d'accueil
        header('Location: /');
        exit;
    }



    // Récupérer les informations du canal
    $query = $dbh->prepare("SELECT * FROM channel WHERE id_channel = :id_channel AND est_actif = 1");
    $query->execute(['id_channel' => $idChannel]);
    $channelExistant = $query->fetch();

    if (!$channelExistant) {
        // Si le canal n'existe pas ou n'est pas actif, rediriger vers la page d'accueil
        header('Location: /');
        exit;
    }

    if ($channelExistant['est_groupe']) {
        // Si le canal est un groupe, utiliser le nom du canal comme titre
        $title = $channelExistant['nom_du_channel'] ?? '';
        $destinataire = [];
        $destinataire['est_actif_destinataire'] = 1;
    } else {
        // Sinon, récupérer les informations de l'autre utilisateur dans la conversation
        $query = $dbh->prepare(
            "SELECT utilisateur.prenom AS prenom_destinataire, utilisateur.nom AS nom_destinataire, utilisateur.est_actif AS est_actif_destinataire
            FROM acces
            INNER JOIN utilisateur ON acces.id_utilisateur = utilisateur.id_utilisateur
            WHERE acces.id_channel = :id_channel AND utilisateur.id_utilisateur != :id_utilisateur"
        );
        $query->execute([
            'id_channel' => $idChannel,
            'id_utilisateur' => $idUtilisateur
        ]);
        $destinataire = $query->fetch();

        if (!$destinataire) {
            // Si l'autre utilisateur n'est pas trouvé, rediriger vers la page d'accueil
            header('Location: /');
            exit;
        }

        // Utiliser le nom et le prénom de l'autre utilisateur comme titre
        $title = ($destinataire['prenom_destinataire'] ?? '') . ' ' . ($destinataire['nom_destinataire'] ?? '');
    }


    // Récupérer les messages de la conversation
    $query = $dbh->prepare(
        "SELECT message.*, utilisateur.prenom, utilisateur.nom 
        FROM message
        INNER JOIN utilisateur ON message.id_utilisateur = utilisateur.id_utilisateur
        WHERE message.id_channel = :id_channel
        ORDER BY date_heure_envoi"
    );
    $query->execute(['id_channel' => $idChannel]);
    $messages = $query->fetchAll();

    if (!$messages) {
        // Si aucun message n'est trouvé, initialiser le tableau des messages à vide
        $messages = [];
    }




    // Gérer l'envoi d'un nouveau message
    if (isset($_POST['message_submit']) && !empty($_POST['contenu'])) {
        // Valider le token CSRF
        if (!csrf_validate()) {
            $_SESSION['error_message'] = "Token de sécurité invalide ou expiré.";
            header("Location: /?page=conversation&id_channel=$idChannel");
            exit;
        }

        // Vérifier si le destinataire est actif avant d'envoyer le message
        if (!$channelExistant['est_groupe'] && !$destinataire['est_actif_destinataire']) {
            $_SESSION['error_message'] = "Impossible d'envoyer des messages à un utilisateur désactivé.";
            header("Location: /?page=conversation&id_channel=$idChannel");
            exit;
        }

        // Insérer le nouveau message dans la base de données
        $query = $dbh->prepare(
            "INSERT INTO message (date_heure_envoi, contenu, id_channel, id_utilisateur)
            VALUES (NOW(), :contenu, :id_channel, :id_utilisateur)"
        );
        $query->execute([
            'contenu' => $_POST['contenu'] ?? '',
            'id_channel' => $idChannel,
            'id_utilisateur' => $idUtilisateur
        ]);

        // Logger l'événement
        error_log("Message envoyé - Channel ID: $idChannel, Utilisateur: $idUtilisateur");

        // Mettre à jour la date du dernier message dans le canal
        $query = $dbh->prepare("UPDATE channel SET date_heure_dernier_message = NOW() WHERE id_channel = :id_channel");
        $query->execute(['id_channel' => $idChannel]);

        // Rediriger pour éviter la soumission multiple de formulaires
        header("Location: /?page=conversation&id_channel=$idChannel");
        exit;
    }


    
} else {
    // Si aucun destinataire ou canal n'est spécifié, rediriger vers la page d'accueil
    header('Location: /');
    exit;
}
