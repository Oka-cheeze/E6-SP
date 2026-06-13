<div class="container mt-5 pt-4" style="background-color: #f8fafd; min-height: 80vh;">
    <div class="row g-4">
        
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 text-center">
                <div class="mb-3">
                    <img src="https://i.pravatar.cc/150?u=<?= $_SESSION['id_user'] ?>" class="rounded-circle shadow-sm" width="120" height="120" style="object-fit: cover; border: 4px solid #fff;">
                </div>
                <h3 class="fw-bold mb-0"><?= ($user['PRENOM_USER'] ?? '') . ' ' . ($user['NOM_USER'] ?? '') ?></h3>
                <p class="text-muted mb-0">
                    <?php 
                        if (isset($user['ROLE_USER']) && $user['ROLE_USER'] == 'admin') {
                            echo '<span class="badge bg-danger rounded-pill">Administrateur</span>';
                        } else {
                            echo ucfirst($user['ROLE_USER'] ?? 'client');
                        }
                    ?>
                </p>
                
                <div class="text-start border-top pt-4">
                    <div class="mb-3">
                        <label class="small text-muted d-block text-uppercase fw-bold">Email</label>
                        <p class="mb-0"><?= $user['EMAIL_USER'] ?? 'Non renseigné' ?></p>
                    </div>
                    <div class="mb-3">
                        <label class="small text-muted d-block text-uppercase fw-bold">Téléphone</label>
                        <p class="mb-0"><?= $user['TEL_USER'] ?? 'Non renseigné' ?></p>
                    </div>
                    <div class="mb-4">
                        <label class="small text-muted d-block text-uppercase fw-bold">Adresse</label>
                        <p class="mb-0"><?= ($user['ADRESSE_USER'] ?? '') . ', ' . ($user['CP_USER'] ?? '') . ' ' . ($user['VILLE_USER'] ?? '') ?></p>
                    </div>
                    <button class="btn btn-outline-primary w-100 rounded-pill mt-4" 
                            data-bs-toggle="modal" 
                            data-bs-target="#modalEditProfile">
                        <i class="bi bi-pencil-square me-2"></i>Modifier mes infos
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <?php if (isset($_GET['success']) && $_GET['success'] == 'updated'): ?>
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert" style="background-color: #d1e7dd; color: #0f5132;">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle-fill me-3 fs-4"></i>
                        <div>
                            <strong class="d-block">Modification réussie !</strong>
                            Vos informations personnelles ont été mises à jour avec succès.
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['confirmed'])): ?>
                <div class="alert alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4 p-4" role="alert"
                     style="background: linear-gradient(135deg, #e8f5e9, #f1f8f5); border-left: 4px solid #4d7c6d !important;">
                    <div class="d-flex align-items-center">
                        <div class="me-3 fs-2">🎿</div>
                        <div>
                            <strong class="d-block fs-5" style="color:#2e7d32;">Réservation confirmée, merci !</strong>
                            <span class="text-muted">Nous vous remercions pour votre confiance. Un email récapitulatif vous a été envoyé. Toute l'équipe Neige et Soleil vous souhaite un séjour exceptionnel !</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 rounded-4 p-3 bg-white">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary text-white rounded-3 p-3 me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="bi bi- luggage-fill"></i>
                            </div>
                            <div>
                                <h6 class="text-muted small text-uppercase mb-0">Mes voyages</h6>
                                <h3 class="fw-bold mb-0">
                                    <?php 
                                        // On compte le nombre de réservations passées par le UserController
                                        echo isset($lesReservations) ? count($lesReservations) : 0; 
                                    ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold mb-0">Mes réservations récentes</h4>
                <span class="badge bg-white text-dark shadow-sm rounded-pill px-3 py-2">
                    <?= isset($lesReservations) ? count($lesReservations) : 0 ?> réservation(s)
                </span>
            </div>

            <div class="row g-4">
                <?php if (empty($lesReservations)): ?>
                    <div class="col-12">
                        <div class="card border-0 shadow-sm rounded-4 p-5 text-center">
                            <i class="bi bi-calendar-x text-muted mb-3" style="font-size: 3rem;"></i>
                            <p class="text-muted mb-0">Vous n'avez pas encore de réservation.</p>
                            <div class="mt-3">
                                <a href="index.php?action=destinations" class="btn btn-primary rounded-pill">Explorer les stations</a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($lesReservations as $res): ?>
                    <div class="col-12">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                            <div class="card-body p-0">
                                <div class="d-flex flex-column flex-md-row">
                                    <!-- Photo de l'habitation -->
                                    <div style="min-width:160px; max-width:160px; flex-shrink:0;">
                                        <img src="<?= htmlspecialchars($res['IMAGE_HABIT'] ?? 'assets/img/locations/default.jpg') ?>"
                                             alt="<?= htmlspecialchars($res['TITRE_HABIT']) ?>"
                                             class="w-100 h-100"
                                             style="object-fit:cover; min-height:130px; max-height:160px;">
                                    </div>
                                    <div class="p-4 flex-grow-1">
                                        <h5 class="fw-bold mb-1"><?= htmlspecialchars($res['TITRE_HABIT']) ?></h5>
                                        <div class="text-muted small mb-3">
                                            <i class="bi bi-calendar3 me-2"></i>Du <?= date('d/m/Y', strtotime($res['DATE_DEBUT_RES'])) ?> au <?= date('d/m/Y', strtotime($res['DATE_FIN_RES'])) ?>
                                            <span class="ms-3"><i class="bi bi-people me-2"></i><?= $res['NB_PERSONNES'] ?> personne(s)</span>
                                        </div>
                                        
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="fw-bold text-primary fs-5">
                                                <?= number_format($res['PRIX_TOTAL_RES'] ?? 0, 2) ?> €
                                            </div>
                                            <span class="badge bg-success-subtle text-success rounded-pill">
                                                <?= ucfirst($res['STATUT_RES']) ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="p-4 text-end bg-light d-flex flex-column justify-content-center align-items-end" style="min-width: 150px;">
                                        <button class="btn btn-sm btn-dark rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalRes<?= $res['ID_RES'] ?>">
                                            <i class="bi bi-eye me-1"></i>Détails
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Modale pour les détails de la réservation -->
                    <div class="modal fade" id="modalRes<?= $res['ID_RES'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content rounded-4 border-0 shadow">
                                <div class="modal-header border-0">
                                    <h5 class="fw-bold">Réservation #<?= $res['ID_RES'] ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <h6 class="fw-bold mb-3"><?= htmlspecialchars($res['TITRE_HABIT']) ?></h6>
                                    <p class="text-muted"><i class="bi bi-geo-alt"></i> <?= $res['VILLE_HABIT'] ?? '' ?></p>
                                    
                                    <div class="row mb-3">
                                        <div class="col-6">
                                            <small class="text-muted d-block">Date d'arrivée</small>
                                            <strong><?= date('d/m/Y', strtotime($res['DATE_DEBUT_RES'])) ?></strong>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted d-block">Date de départ</small>
                                            <strong><?= date('d/m/Y', strtotime($res['DATE_FIN_RES'])) ?></strong>
                                        </div>
                                    </div>
                                    
                                    <div class="alert alert-light border mb-0">
                                        <div class="d-flex justify-content-between">
                                            <span>Total payé</span>
                                            <strong class="text-primary"><?= number_format($res['PRIX_TOTAL_RES'] ?? 0, 2) ?> €</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
// Réservations se terminant aujourd'hui — invitation à évaluer
if (isset($_SESSION['id_user'])) {
    $unModele_profile = $user; // déjà chargé
    // On utilise la variable $lesReservations déjà disponible pour filtrer
    $a_evaluer = array_filter($lesReservations ?? [], function($r) {
        return $r['STATUT_RES'] === 'confirmee'
            && $r['DATE_FIN_RES'] === date('Y-m-d')
            && empty($r['NOTE_AVIS']); // si jointure avis présente
    });
}
?>

<?php if (!empty($a_evaluer)): ?>
<div class="alert border-0 shadow-sm rounded-4 mb-4 p-4" style="background: linear-gradient(135deg, #fff8e1, #fff3cd);">
    <div class="d-flex align-items-center mb-3">
        <i class="bi bi-star-fill text-warning fs-3 me-3"></i>
        <div>
            <h5 class="fw-bold mb-0">Votre séjour se termine aujourd'hui !</h5>
            <p class="text-muted small mb-0">Partagez votre expérience pour aider les autres voyageurs.</p>
        </div>
    </div>
    <?php foreach($a_evaluer as $r): ?>
    <div class="d-flex justify-content-between align-items-center bg-white rounded-3 p-3 mb-2 shadow-sm">
        <div>
            <strong><?= htmlspecialchars($r['TITRE_HABIT']) ?></strong>
            <span class="text-muted small ms-2">
                du <?= date('d/m/Y', strtotime($r['DATE_DEBUT_RES'])) ?>
                au <?= date('d/m/Y', strtotime($r['DATE_FIN_RES'])) ?>
            </span>
        </div>
        <button class="btn btn-warning btn-sm rounded-pill px-3 fw-bold"
                data-bs-toggle="modal"
                data-bs-target="#modalAvis<?= $r['ID_RES'] ?>">
            <i class="bi bi-star me-1"></i>Évaluer
        </button>
    </div>

    <!-- Modale d'évaluation -->
    <div class="modal fade" id="modalAvis<?= $r['ID_RES'] ?>" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <form action="index.php?action=soumettre_avis" method="POST">
                    <input type="hidden" name="id_res" value="<?= $r['ID_RES'] ?>">
                    <div class="modal-header border-0">
                        <h5 class="fw-bold">Évaluer votre séjour</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <h6 class="fw-bold mb-1"><?= htmlspecialchars($r['TITRE_HABIT']) ?></h6>
                        <p class="text-muted small mb-4">
                            <?= date('d/m/Y', strtotime($r['DATE_DEBUT_RES'])) ?>
                            → <?= date('d/m/Y', strtotime($r['DATE_FIN_RES'])) ?>
                        </p>

                        <!-- Étoiles interactives -->
                        <div class="mb-4 text-center">
                            <label class="form-label fw-bold d-block mb-2">Votre note</label>
                            <div class="star-rating d-flex justify-content-center gap-2" id="stars-<?= $r['ID_RES'] ?>">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                <i class="bi bi-star fs-2 text-warning star-btn"
                                   data-value="<?= $i ?>"
                                   data-res="<?= $r['ID_RES'] ?>"
                                   style="cursor:pointer; transition: transform 0.1s;">
                                </i>
                                <?php endfor; ?>
                            </div>
                            <input type="hidden" name="note" id="note-<?= $r['ID_RES'] ?>" value="0" required>
                            <small class="text-muted mt-1 d-block" id="note-label-<?= $r['ID_RES'] ?>">Cliquez pour noter</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Commentaire <span class="text-muted fw-normal">(optionnel)</span></label>
                            <textarea name="commentaire" class="form-control rounded-3" rows="4"
                                      placeholder="Décrivez votre expérience : accueil, propreté, confort..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold"
                                id="btn-submit-<?= $r['ID_RES'] ?>" disabled>
                            <i class="bi bi-send me-1"></i>Envoyer mon avis
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<script>
document.querySelectorAll('.star-btn').forEach(function(star) {
    star.addEventListener('click', function() {
        const val  = parseInt(this.dataset.value);
        const res  = this.dataset.res;
        const container = document.getElementById('stars-' + res);

        // Remplir les étoiles
        container.querySelectorAll('.star-btn').forEach(function(s) {
            const sv = parseInt(s.dataset.value);
            s.classList.toggle('bi-star-fill', sv <= val);
            s.classList.toggle('bi-star',      sv >  val);
        });

        document.getElementById('note-' + res).value = val;
        document.getElementById('note-label-' + res).textContent = val + ' étoile' + (val > 1 ? 's' : '');
        document.getElementById('btn-submit-' + res).disabled = false;
    });

    star.addEventListener('mouseover', function() {
        const val = parseInt(this.dataset.value);
        const res = this.dataset.res;
        document.getElementById('stars-' + res).querySelectorAll('.star-btn').forEach(function(s) {
            s.style.transform = parseInt(s.dataset.value) <= val ? 'scale(1.2)' : 'scale(1)';
        });
    });

    star.addEventListener('mouseout', function() {
        const res = this.dataset.res;
        document.getElementById('stars-' + res).querySelectorAll('.star-btn').forEach(function(s) {
            s.style.transform = 'scale(1)';
        });
    });
});
</script>
<?php endif; ?>

<div class="modal fade" id="modalEditProfile" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0">
                <h5 class="fw-bold mb-0">Modifier mon profil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="index.php?action=edit_profile" method="POST">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Prénom</label>
                            <input type="text" name="prenom" class="form-control rounded-3" value="<?= $user['PRENOM_USER'] ?? '' ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nom</label>
                            <input type="text" name="nom" class="form-control rounded-3" value="<?= $user['NOM_USER'] ?? '' ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Email</label>
                            <input type="email" name="email" class="form-control rounded-3" value="<?= $user['EMAIL_USER'] ?? '' ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Téléphone</label>
                            <input type="text" name="tel" class="form-control rounded-3" value="<?= $user['TEL_USER'] ?? '' ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Adresse</label>
                            <input type="text" name="adresse" class="form-control rounded-3" value="<?= $user['ADRESSE_USER'] ?? '' ?>">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">Ville</label>
                            <input type="text" name="ville" class="form-control rounded-3" value="<?= $user['VILLE_USER'] ?? '' ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">CP</label>
                            <input type="text" name="cp" class="form-control rounded-3" value="<?= $user['CP_USER'] ?? '' ?>">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" name="valider_modif" class="btn btn-primary rounded-pill px-4">Enregistrer les modifications</button>
                </div>
            </form>
        </div>
    </div>
</div>