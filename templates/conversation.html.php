<!DOCTYPE html>
<html lang="fr">

<head>
    <!-- Encodage des caractères et paramétrage pour les appareils mobiles -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Lien vers le fichier CSS spécifique à la page de conversation -->
    <link rel="stylesheet" href="assets/css/conversation.css">
    <title>Conversation</title>
</head>

<body>
    <div class="content-right-section">
        <div class="chat-container">
            <!-- Affichage du titre de la conversation -->
            <h2><?= e($title) ?></h2>

            <!-- Affichage d'un message d'erreur si disponible dans la session -->
            <?php if (isset($_SESSION['error_message'])) : ?>
                <div class="error-message">
                    <!-- Icône d'avertissement SVG -->
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                        <path fill="currentColor" d="M12 0C5.37 0 0 5.37 0 12s5.37 12 12 12 12-5.37 12-12S18.63 0 12 0zm1 17.25h-2v-2h2v2zm0-4h-2v-6h2v6z" />
                    </svg>
                    <!-- Message d'erreur récupéré de la session -->
                    <div><?= e($_SESSION['error_message']) ?></div>
                </div>
                <!-- Supprime le message d'erreur après affichage -->
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>

            <div class="message-container">
                <!-- Vérifie s'il y a des messages à afficher -->
                <?php if (!empty($messages)) : ?>
                    <!-- Boucle sur les messages récupérés de la base de données -->
                    <?php foreach ($messages as $msg) : ?>
                        <!-- Si l'utilisateur est l'expéditeur, le message est envoyé, sinon il est reçu -->
                        <div class="message <?= $msg['id_utilisateur'] == $_SESSION['id_utilisateur'] ? 'sent' : 'received' ?>">
                            <!-- Affiche le nom de l'expéditeur -->
                            <p class="message-author"><?= e($msg['prenom'] . ' ' . $msg['nom']) ?>:</p>
                            <!-- Affiche le contenu du message -->
                            <p class="message-content"><?= nl2br(e($msg['contenu'])) ?></p>
                            <!-- Affiche la date et l'heure du message -->
                            <p class="message-time"><?= e(date('d/m/Y H:i', strtotime($msg['date_heure_envoi']))) ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <!-- Si aucun message n'est disponible, affiche un message par défaut -->
                    <p>Aucun message à afficher pour le moment.</p>
                <?php endif; ?>
            </div>

            <!-- Affiche le formulaire d'envoi de message si l'utilisateur est actif ou si c'est un groupe -->
            <?php if ($channelExistant['est_groupe'] || $destinataire['est_actif_destinataire']) : ?>
                <form id="message-form" method="POST" class="message-form">
                    <?= csrf_field() ?>
                    <div class="form-controls">
                        <!-- Champ texte pour entrer le message -->
                        <textarea name="contenu" placeholder="Saisissez votre message ici..." rows="1" required></textarea>
                        <!-- Bouton pour envoyer le message -->
                        <button type="submit" name="message_submit" id="messageSubmit">
                            <!-- Icône d'envoi SVG -->
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                                <path fill="currentColor" d="M2 21l21-9L2 3v7l15 2-15 2z"></path>
                            </svg>
                        </button>
                    </div>
                </form>
            <?php else : ?>
                <!-- Message d'erreur si l'utilisateur est désactivé -->
                <div class="error-message">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                        <path fill="currentColor" d="M12 0C5.37 0 0 5.37 0 12s5.37 12 12 12 12-5.37 12-12S18.63 0 12 0zm1 17.25h-2v-2h2v2zm0-4h-2v-6h2v6z" />
                    </svg>
                    <!-- Message d'erreur si l'utilisateur est inactif -->
                    <div>Vous ne pouvez pas envoyer de messages à cet utilisateur car il est désactivé.</div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>