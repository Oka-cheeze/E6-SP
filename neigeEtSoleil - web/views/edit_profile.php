<div class="container mt-5 pt-5">
    <div class="row justify-content-center">
        <div class="col-md-7 shadow-lg p-5 rounded-4 bg-white">
            <h3 class="fw-bold mb-4">Modifier mes coordonnées</h3>
            <form method="post">
                <input type="hidden" name="email" value="<?= $user['EMAIL_USER'] ?>">

                <div class="row mb-3">
                    <div class="col">
                        <label class="small fw-bold">Nom</label>
                        <input type="text" name="nom" class="form-control rounded-pill" value="<?= $user['NOM_USER'] ?>" required>
                    </div>
                    <div class="col">
                        <label class="small fw-bold">Prénom</label>
                        <input type="text" name="prenom" class="form-control rounded-pill" value="<?= $user['PRENOM_USER'] ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="small fw-bold">Adresse</label>
                    <input type="text" name="adresse" class="form-control rounded-pill" 
                        value="<?= $user['ADRESSE_USER'] ?>" required> 

                    <input type="text" name="cp" class="form-control rounded-pill" 
                        value="<?= $user['CP_USER'] ?>" required>

                    <input type="text" name="tel" class="form-control rounded-pill" 
                        value="<?= $user['TEL_USER'] ?>" required>
                                    </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="small fw-bold">Code Postal</label>
                        <input type="text" name="cp" class="form-control rounded-pill" value="<?= $user['CP_CLI'] ?? $user['CP_PRO'] ?>" required>
                    </div>
                    <div class="col-md-8">
                        <label class="small fw-bold">Ville</label>
                        <input type="text" name="ville" class="form-control rounded-pill" value="<?= $user['VILLE_USER'] ?>" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="small fw-bold">Téléphone</label>
                    <input type="text" name="tel" class="form-control rounded-pill" value="<?= $user['TEL_CLI'] ?? $user['TEL_PRO'] ?>" required>
                </div>

                <?php if ($_SESSION['role'] === 'proprietaire'): ?>
                <div class="mb-4">
                    <label class="small fw-bold text-primary">RIB (Propriétaire uniquement)</label>
                    <input type="text" name="rib" class="form-control rounded-pill" value="<?= $user['RIB_PRO'] ?? '' ?>" placeholder="FR76...">
                </div>
                <?php endif; ?>

                <div class="d-flex gap-2">
                    <button type="submit" name="valider_modif" class="btn btn-primary rounded-pill px-4" style="background-color: #4d7c6d; border:none;">Enregistrer les modifications</button>
                    <a href="index.php?action=profile" class="btn btn-light rounded-pill px-4">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>