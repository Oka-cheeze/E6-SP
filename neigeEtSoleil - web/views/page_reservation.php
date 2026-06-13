<div class="container mt-5 pt-5 mb-5">
    <div class="row mb-4">
        <div class="col-12">
            <div class="rounded-4 overflow-hidden shadow-sm" style="height: 400px;">
                <img src="<?= $habitation['IMAGE_HABIT'] ?>" class="w-100 h-100" style="object-fit: cover;">
            </div>
        </div>
    </div>

    <div class="row g-5">
        <div class="col-lg-8">
            <h1 class="fw-bold"><?= $habitation['VILLE_HABIT'] ?></h1>
            <p class="text-muted fs-5"><?= $habitation['CP_HABIT'] ?> • <?= $habitation['NB_CHAMBRES_HABIT'] ?> pièces • <?= $habitation['NB_GUETS_HABIT'] ?> voyageurs</p>
            <hr class="my-4">
            <h4 class="fw-bold">À propos de ce logement</h4>
            <p class="text-secondary fs-6 lh-lg"><?= $habitation['DESCRIPTION_HABIT'] ?></p>
            
            <hr class="my-5">

            <h4 class="fw-bold mb-4">Activités recommandées à <?= $habitation['VILLE_HABIT'] ?></h4>
            <div class="row g-3">
                <?php foreach($activites as $act): ?>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4 p-3 text-center">
                        <h6 class="fw-bold mb-1"><?= htmlspecialchars($act['NOM_ACT']) ?></h6>
                        <p class="small text-muted mb-3"><?= $act['PRIX_ACT'] ?>€ / pers.</p>
                        <button class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalAct<?= $act['ID_ACT'] ?>">Réserver</button>
                    </div>
                </div>

                <div class="modal fade" id="modalAct<?= $act['ID_ACT'] ?>" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content rounded-4 border-0 shadow">
                            <form action="index.php?action=ajouter_activite_panier" method="POST">
                                <div class="modal-header border-0 pb-0">
                                    <h5 class="fw-bold"><?= htmlspecialchars($act['NOM_ACT']) ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="small fw-bold mb-1">Date souhaitée</label>
                                        <input type="date" name="date_act" class="form-control rounded-3" required min="<?= date('Y-m-d') ?>">
                                    </div>
                                    <input type="hidden" name="id_act" value="<?= $act['ID_ACT'] ?>">
                                    <input type="hidden" name="nom_act" value="<?= htmlspecialchars($act['NOM_ACT']) ?>">
                                    <input type="hidden" name="prix_act" value="<?= $act['PRIX_ACT'] ?>">
                                </div>
                                <div class="modal-footer border-0 pt-0">
                                    <button type="submit" class="btn btn-primary w-100 py-2 rounded-pill fw-bold" style="background-color: #4d7c6d; border:none;">Ajouter à ma réservation</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-lg rounded-4 p-4 sticky-top" style="top: 100px;">
                <div class="mb-4">
                    <span class="fs-3 fw-bold"><?= $habitation['PRIX_NUIT_HABIT'] ?>€</span>
                    <span class="text-muted">/ nuit</span>
                </div>

                <?php if (isset($_GET['error']) && $_GET['error'] == 'date_manquante'): ?>
                    <div class="alert alert-danger border-0 small mb-3 py-2">
                        <i class="bi bi-exclamation-circle me-2"></i>Veuillez sélectionner vos dates.
                    </div>
                <?php endif; ?>

                <form action="index.php?action=ajouter_panier" method="POST">
                    <input type="hidden" name="id_habit" value="<?= $habitation['ID_HABIT'] ?>">
                    
                    <div class="mb-3">
                        <label class="small fw-bold text-muted text-uppercase mb-1">Dates de séjour</label>
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-calendar-event"></i></span>
                            <input type="text" name="dates_sejour" id="dates_details" 
                                class="form-control border-start-0 ps-0 shadow-none" 
                                placeholder="Sélectionner vos dates" 
                                value="<?= htmlspecialchars($dates_preselectionnees ?? '') ?>" required>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-sm" style="background-color: #4d7c6d; border:none;">
                        Réserver ce logement
                    </button>
                </form>
                
                <p class="text-center text-muted small mt-3 mb-0">Aucun montant ne sera débité pour le moment</p>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof flatpickr !== 'undefined') {
            flatpickr("#dates_details", {
                mode: "range",
                minDate: "today",
                dateFormat: "d/m/Y",
                locale: "fr"
            });
        }
    });
</script>