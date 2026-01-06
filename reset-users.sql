-- Script de réinitialisation des utilisateurs
-- Ce script supprime tous les utilisateurs et crée un compte administrateur par défaut

-- Désactiver temporairement les contraintes de clés étrangères
SET FOREIGN_KEY_CHECKS = 0;

-- Supprimer toutes les données liées (messages, groupes, etc.)
DELETE FROM message;
DELETE FROM acces;
DELETE FROM channel;
DELETE FROM password_reset;

-- Supprimer tous les utilisateurs existants
DELETE FROM utilisateur;

-- Réactiver les contraintes de clés étrangères
SET FOREIGN_KEY_CHECKS = 1;

-- Réinitialiser l'auto-increment
ALTER TABLE utilisateur AUTO_INCREMENT = 1;

-- Créer le compte administrateur
INSERT INTO utilisateur (prenom, nom, email, mot_de_passe, date_de_creation, est_admin, est_actif)
VALUES (
    'Administrateur',
    'Système',
    'admin@msn-connect.local',
    '$2y$10$1TIhibewfdcR6Rev/IlFsONDlBIyB8a1AH.DA91/iWq7q4gjrxvZ.',
    NOW(),
    1,
    1
);

-- Créer le compte Clémence Denis (Admin)
INSERT INTO utilisateur (prenom, nom, email, mot_de_passe, date_de_creation, est_admin, est_actif)
VALUES (
    'Clémence',
    'Denis',
    'clem.denis45@gmail.com',
    '$2y$10$.fYaaPJqcMe5SInJGG6X8.xC0ZjJO6DC1CWhM5QIFnJcTjxCnP3Ne',
    NOW(),
    1,
    1
);

-- Créer le compte Cindy Singer (Admin)
INSERT INTO utilisateur (prenom, nom, email, mot_de_passe, date_de_creation, est_admin, est_actif)
VALUES (
    'Cindy',
    'Singer',
    'cindy_singer913@msn.com',
    '$2y$10$GyTSwsVXPtlD624lvpB5UOsLM8cA.hoAewyaq92sVokNVGm0LtKaW',
    NOW(),
    1,
    1
);

-- Créer le compte test
INSERT INTO utilisateur (prenom, nom, email, mot_de_passe, date_de_creation, est_admin, est_actif)
VALUES (
    'Test',
    'Utilisateur',
    'test@msn-connect.local',
    '$2y$10$YxQRY.eB4HHj0fj/pzLeseKUIHMhGXKSfaV3ODZtXsmoW.a.B7Tw6',
    NOW(),
    0,
    1
);

-- Afficher les utilisateurs créés
SELECT id_utilisateur, prenom, nom, email, est_admin, est_actif
FROM utilisateur;
