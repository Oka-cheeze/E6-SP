<div class="container mt-5 pt-5">
    <div class="row justify-content-center">
        <div class="col-md-5 shadow-lg p-5 rounded-4 bg-white border-0">

            <?php if (!empty($error_login)): ?>
            <div class="alert alert-danger rounded-3 mb-3">
                <?= htmlspecialchars($error_login) ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($error_inscription)): ?>
            <div class="alert alert-danger rounded-3 mb-3">
                <?= htmlspecialchars($error_inscription) ?>
            </div>
            <?php endif; ?>

            <?php if (isset($_GET['success']) && $_GET['success'] === 'inscrit'): ?>
            <div class="alert alert-success rounded-3 mb-3">
                Inscription réussie ! Connectez-vous.
            </div>
            <?php endif; ?>

            <?php
            // Si erreur d'inscription, garder le formulaire d'inscription ouvert
            $show_register = !empty($error_inscription) ? 'block' : 'none';
            $show_login    = !empty($error_inscription) ? 'none'  : 'block';
            ?>

            <div id="auth-login" style="display: <?= $show_login ?>;">
                <h3 class="fw-bold mb-4 text-center">Bon retour parmi nous</h3>
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Email</label>
                        <input type="email" name="email" class="form-control rounded-pill px-3" placeholder="exemple@mail.com" required>
                    </div>
                    <div class="mb-4 position-relative">
                        <label class="form-label small fw-bold">Mot de passe</label>
                        <input type="password" name="mdp" id="mdp_login" class="form-control rounded-pill px-3" placeholder="••••••••" required>
                        <i class="bi bi-eye position-absolute end-0 top-50 translate-middle-y me-3 mt-2 btn-toggle-eye" onclick="togglePass('mdp_login')"></i>
                    </div>
                    <button type="submit" name="valider_connexion" class="btn btn-primary w-100 rounded-pill py-2 fw-bold" style="background-color: #4d7c6d; border:none;">
                        Se connecter
                    </button>
                </form>
                <p class="mt-4 text-center small">
                    Nouveau ici ? <a href="javascript:void(0)" onclick="toggleAuth()" class="text-primary fw-bold text-decoration-none">Créez un compte</a>
                </p>
            </div>

            <div id="auth-register" style="display: <?= $show_register ?>;">
                <h3 class="fw-bold mb-4 text-center">Rejoignez l'aventure</h3>
                <form method="post">
                    <div class="row g-2">
                        <div class="col-md-6 mb-3">
                            <input type="text" name="nom" placeholder="Nom" class="form-control rounded-pill px-3"
                                   pattern="[A-Za-z\s\-]+" title="Pas de chiffres autorisés" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <input type="text" name="prenom" placeholder="Prénom" class="form-control rounded-pill px-3"
                                   pattern="[A-Za-z\s\-]+" title="Pas de chiffres autorisés" required>
                        </div>
                    </div>

                    <div class="mb-4 p-3 bg-light rounded-4">
                        <label class="form-label small fw-bold">Je souhaite :</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="role" value="client" id="role_client" onclick="toggleChampsSpecifiques('client')" checked>
                                <label class="form-check-label small" for="role_client">Louer un bien</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="role" value="proprietaire" id="role_proprio" onclick="toggleChampsSpecifiques('proprietaire')">
                                <label class="form-check-label small" for="role_proprio">Publier une annonce</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3" id="bloc_client">
                        <label class="small fw-bold ms-2">Date de naissance</label>
                        <input type="date" name="date_n" id="input_date_n" class="form-control rounded-pill px-3" required>
                    </div>

                    <div class="mb-3" id="bloc_proprio" style="display:none;">
                        <input type="text" name="rib" id="input_rib" placeholder="Votre RIB (IBAN)" class="form-control rounded-pill px-3">
                    </div>

                    <input type="text" name="adresse" placeholder="Adresse complète" class="form-control rounded-pill px-3 mb-3" required>

                    <div class="row g-2">
                        <div class="col-md-4 mb-3">
                            <input type="text" name="cp" placeholder="CP" class="form-control rounded-pill px-3"
                                   pattern="[0-9]{5}" title="5 chiffres requis" required>
                        </div>
                        <div class="col-md-8 mb-3">
                            <input type="text" name="ville" placeholder="Ville" class="form-control rounded-pill px-3" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <input type="email" name="email" placeholder="Email" class="form-control rounded-pill px-3" required>
                    </div>
                    <div class="mb-4 position-relative">
                        <input type="password" name="mdp" id="mdp_register" class="form-control rounded-pill px-3" placeholder="Mot de passe" required>
                        <i class="bi bi-eye position-absolute end-0 top-50 translate-middle-y me-3 btn-toggle-eye" onclick="togglePass('mdp_register')"></i>
                    </div>

                    <button type="submit" name="valider_inscription" class="btn btn-dark w-100 rounded-pill py-2 fw-bold">
                        S'inscrire
                    </button>
                </form>
                <p class="mt-4 text-center small">
                    Déjà un compte ? <a href="javascript:void(0)" onclick="toggleAuth()" class="text-primary fw-bold text-decoration-none">Connectez-vous</a>
                </p>
            </div>

        </div>
    </div>
</div>

<style>
    .btn-toggle-eye { cursor: pointer; color: #6c757d; z-index: 10; }
    .btn-toggle-eye:hover { color: #4d7c6d; }
    #bloc_client, #bloc_proprio { transition: all 0.3s ease; }
</style>

<script>
function toggleAuth() {
    const loginDiv    = document.getElementById('auth-login');
    const registerDiv = document.getElementById('auth-register');
    loginDiv.style.display    = (loginDiv.style.display    === 'none') ? 'block' : 'none';
    registerDiv.style.display = (registerDiv.style.display === 'none') ? 'block' : 'none';
}

function togglePass(id) {
    const input = document.getElementById(id);
    const icon  = event.target;
    if (input.type === "password") {
        input.type = "text";
        icon.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
        input.type = "password";
        icon.classList.replace('bi-eye-slash', 'bi-eye');
    }
}

function toggleChampsSpecifiques(role) {
    const blocClient  = document.getElementById('bloc_client');
    const blocProprio = document.getElementById('bloc_proprio');
    const inputDate   = document.getElementById('input_date_n');

    if (role === 'client') {
        blocClient.style.display  = 'block';
        blocProprio.style.display = 'none';
        inputDate.required = true;
    } else {
        blocClient.style.display  = 'none';
        blocProprio.style.display = 'block';
        inputDate.required = false;
    }
}
</script>