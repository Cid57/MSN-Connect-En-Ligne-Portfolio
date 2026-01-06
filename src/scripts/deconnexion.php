<?php

// Effacer la session
session_destroy();
header("Location: /?page=connexion");
exit;