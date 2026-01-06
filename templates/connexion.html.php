<div class="inscription">
    <!-- Barre de navigation avec le logo de l'application -->
    <nav>
        <div class="vp-logo">
            <img src="assets\img\icon20-removebg-preview.png" width="auto" height="80px" alt="logo">
        </div>
    </nav>
    <main>
        <section class="login-wrap">
            <div class="login-html">
                <!-- Image décorative -->
                <div class="login-image">
                    <img src="assets/img/icon20-removebg-preview.png" alt="MSN Connect">
                </div>

                <!-- Onglet pour la connexion -->
                <header>
                    <label for="tab-1" class="tab">Connexion</label>
                </header>

                <div class="login-form">
                    <!-- Formulaire de connexion -->
                    <form method="POST">
                        <?= csrf_field() ?>
                        <div class="sign-in-htm">
                            <!-- Champ pour l'email avec validation d'erreur -->
                            <div class="group">
                                <label class="label">Email</label>
                                <!-- Le champ email récupère et affiche la valeur précédemment soumise (si disponible) -->
                                <input type="email" class="input" name="email-connexion" value="<?= e($_POST['email-connexion'] ?? '') ?>" placeholder="votre@email.com" required>
                                <!-- PHP vérifie si une erreur existe pour l'email et l'affiche si c'est le cas -->
                                <?php if (!empty($errors['email-connexion'])) : ?>
                                    <p class="message-erreur" aria-live="assertive"><?= e($errors['email-connexion']) ?></p>
                                <?php endif ?>
                            </div>
                            <!-- Champ pour le mot de passe avec option de masquage/affichage -->
                            <div class="group">
                                <label for="pass" class="label">Mot de passe</label>
                                <input type="password" id="mdp1" class="input mdp-input" name="mdp-connexion" placeholder="••••••••" required>
                                <div class="mdp-icon">
                                    <i data-feather="eye" class="eye" style="display: none;"></i>
                                    <i data-feather="eye-off" class="eye-off"></i>
                                </div>
                                <!-- PHP vérifie si une erreur existe pour le mot de passe et l'affiche si c'est le cas -->
                                <?php if (!empty($errors['mdp-connexion'])) : ?>
                                    <p class="message-erreur" aria-live="assertive"><?= e($errors['mdp-connexion']) ?></p>
                                <?php endif ?>
                            </div>
                            <!-- PHP vérifie si une erreur générale de connexion existe et l'affiche si c'est le cas -->
                            <?php if (!empty($errors['login_error'])) : ?>
                                <p class="message-erreur" aria-live="assertive"><?= e($errors['login_error']) ?></p>
                            <?php endif ?>

                            <div class="group">
                                <button class="button" type="submit" name="connexion-submit" aria-label="Connexion sécurisée">Se connecter</button>
                            </div>

                            <hr class="hr">
                            <footer class="foot-lnk">
                                <a href="/?page=mdp-reset">Mot de passe oublié ?</a>
                            </footer>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </main>
</div>