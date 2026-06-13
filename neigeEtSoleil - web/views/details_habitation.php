<div class="container mt-5 pt-5 mb-5">

    <div class="row mb-4">
        <div class="col-12">
            <?php $galerie = $unModele->getPhotosByHabit($habitation['ID_HABIT']); ?>
            <div id="carouselDetails" class="carousel slide rounded-4 overflow-hidden shadow-sm" data-bs-ride="carousel">
                <div class="carousel-inner" style="height: 400px;">
                    <div class="carousel-item active h-100">
                        <img src="<?= $habitation['IMAGE_HABIT'] ?>" class="d-block w-100 h-100" style="object-fit: cover;">
                    </div>
                    <?php foreach($galerie as $p): ?>
                        <div class="carousel-item h-100">
                            <img src="<?= htmlspecialchars($p['CHEMIN_PHOTO'] ?? '') ?>" class="d-block w-100 h-100" style="object-fit: cover;">
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if(!empty($galerie)): ?>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselDetails" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselDetails" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row g-5">
        <div class="col-lg-8">
            <h1 class="fw-bold"><?= htmlspecialchars($habitation['TITRE_HABIT']) ?></h1>
            <p class="text-muted small mb-1" style="color:#4d7c6d;"><?= htmlspecialchars($habitation['VILLE_HABIT']) ?></p>
            <p class="text-muted fs-5"><?= $habitation['CP_HABIT'] ?> • <?= $habitation['NB_CHAMBRES_HABIT'] ?> pièces • <?= $habitation['NB_GUETS_HABIT'] ?> voyageurs</p>
            <hr class="my-4">
            <h4 class="fw-bold">À propos de ce logement</h4>
            <p class="text-secondary fs-6 lh-lg"><?= $habitation['DESCRIPTION_HABIT'] ?></p>

            <hr class="my-5">

            <!-- ── ACTIVITÉS À PROXIMITÉ (lecture seule) ── -->
            <h4 class="fw-bold mb-1">
                <i class="bi bi-geo-alt-fill me-2" style="color:#4d7c6d;"></i>Activités à proximité
            </h4>
            <p class="text-muted small mb-4">À <?= htmlspecialchars($habitation['VILLE_HABIT']) ?> et dans la station</p>

            <?php if (!empty($activites)): ?>
            <div class="row g-3">
                <?php foreach($activites as $act): ?>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                        <div class="card-body d-flex flex-column">
                            <span class="badge mb-2 align-self-start" style="background-color:#e8f5e9; color:#4d7c6d; font-weight:600;">
                                <i class="bi bi-tag me-1"></i><?= htmlspecialchars($act['TYPE_ACT'] ?? 'Activité') ?>
                            </span>
                            <h6 class="fw-bold mb-1"><?= htmlspecialchars($act['NOM_ACT'] ?? '') ?></h6>
                            <p class="text-muted small mb-3 flex-grow-1"><?= htmlspecialchars($act['DESCRIPTION_ACT'] ?? 'Aucune description') ?></p>
                            <p class="fw-bold mb-0" style="color:#4d7c6d;">
                                <i class="bi bi-currency-euro me-1"></i><?= $act['PRIX_ACT'] ?>€ / pers.
                            </p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
                <div class="alert alert-light border text-center py-4">
                    <i class="bi bi-info-circle text-muted fs-4 d-block mb-2"></i>
                    <p class="mb-0 text-muted">Aucune activité référencée dans cette station pour le moment.</p>
                </div>
            <?php endif; ?>
            <!-- ── FIN ACTIVITÉS À PROXIMITÉ ── -->
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

                <form action="index.php?action=ajouter_panier" method="POST" id="formReservation">
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

                    <div class="border-top pt-3 mb-3" id="recapitulatif" style="display: none;">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Prix par nuit</span>
                            <span class="small"><?= $habitation['PRIX_NUIT_HABIT'] ?>€</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Nombre de nuits</span>
                            <span class="small" id="nb_nuits">-</span>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between">
                            <span class="fw-bold">Total</span>
                            <span class="fw-bold text-primary" id="total_sejour">-</span>
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

<style>
.flatpickr-day.flatpickr-disabled,
.flatpickr-day.flatpickr-disabled:hover {
    background-color: #f8d7da !important;
    color: #842029 !important;
    border-color: #f5c2c7 !important;
    text-decoration: line-through;
    cursor: not-allowed;
    opacity: 1 !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputDates = document.getElementById('dates_details');
    const dateValue  = inputDates ? inputDates.value : '';
    const prixNuit   = <?= (int)$habitation['PRIX_NUIT_HABIT'] ?>;
    const disableDates = <?= json_encode(!empty($disableDates) ? $disableDates : []) ?>;

    const fp = flatpickr("#dates_details", {
        mode: "range",
        minDate: "today",
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "d/m/Y",
        disable: disableDates,
        locale: flatpickr.l10ns.fr,
        showMonths: 1,
        appendTo: document.body,
        onReady: function(s, d, instance) {
            instance.calendarContainer.style.zIndex = "99999";
        },
        onOpen: function(s, d, instance) {
            instance.calendarContainer.style.zIndex = "99999";
        },
        defaultDate: dateValue ? dateValue.split(" au ") : null,
        onChange: function(selectedDates) {
            if (selectedDates.length === 2) {
                const diffTime = Math.abs(selectedDates[1] - selectedDates[0]);
                const nbNuits  = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                if (nbNuits > 0) {
                    document.getElementById('recapitulatif').style.display = 'block';
                    document.getElementById('nb_nuits').textContent = nbNuits + ' nuit' + (nbNuits > 1 ? 's' : '');
                    document.getElementById('total_sejour').textContent = (nbNuits * prixNuit) + '€';
                }
            } else {
                document.getElementById('recapitulatif').style.display = 'none';
            }
        }
    });

    if (fp && fp.selectedDates.length === 2) {
        fp.config.onChange(fp.selectedDates);
    }
});
</script>