<?php
$password = '123456';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Email: clem.denis45@gmail.com\n";
echo "Mot de passe: $password\n";
echo "Hash: $hash\n\n";

echo "Vérification: " . (password_verify($password, $hash) ? "✓ OK" : "✗ ERREUR") . "\n";
