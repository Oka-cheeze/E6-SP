<section class="hero-section">
    <div class="container text-center">
        <h1 class="text-white fw-bold display-3">Votre cocon en altitude</h1>
        <p class="text-white fs-5 mt-3 shadow-sm">Découvrez nos locations d'exception pour vos séjours à la montagne.</p>
    </div>
</section>

<div class="container search-wrapper">
    <div class="filter-bar shadow-lg mx-auto">
        <form action="index.php" method="GET" class="row g-0 align-items-center">
            <input type="hidden" name="action" value="rechercher">

            <!-- Destination : ville, région ou station -->
            <div class="col-md-4 px-4 py-3 d-flex align-items-center">
                <i class="bi bi-geo-alt-fill text-muted me-2"></i>
                <div class="w-100">
                    <label class="d-block small fw-bold text-uppercase text-muted" style="font-size: 0.7rem;">Destination</label>
                    <input type="text" name="destination" id="destination_input"
                           class="form-control border-0 p-0 shadow-none bg-transparent"
                           placeholder="Ville, région ou station..."
                           pattern="[A-Za-z\u00C0-\u00FF\s\-']+"
                           title="La destination ne peut contenir que des lettres, espaces et tirets."
                           value="<?= htmlspecialchars($_GET['destination'] ?? '') ?>">
                </div>
            </div>

            <!-- Dates -->
            <div class="col-md-3 px-4 py-3 border-start d-flex align-items-center">
                <i class="bi bi-calendar-range text-muted me-2"></i>
                <div class="w-100">
                    <label class="d-block small fw-bold text-uppercase text-muted" style="font-size: 0.7rem;">Séjour</label>
                    <input type="text" id="dates_sejour" name="dates" class="form-control border-0 p-0 shadow-none bg-transparent"
                           placeholder="Ajouter des dates"
                           value="<?= htmlspecialchars($_GET['dates'] ?? '') ?>">
                </div>
            </div>

            <!-- Voyageurs : valeur vide par défaut = non pris en compte -->
            <div class="col-md-3 px-4 py-3 border-start d-flex align-items-center">
                <i class="bi bi-people text-muted me-2"></i>
                <div class="w-100">
                    <label class="d-block small fw-bold text-uppercase text-muted" style="font-size: 0.7rem;">Voyageurs</label>
                    <select name="voyageurs" class="form-select border-0 p-0 shadow-none bg-transparent">
                        <option value="" <?= empty($_GET['voyageurs']) ? 'selected' : '' ?>>Nombre de voyageurs</option>
                        <option value="1"  <?= (($_GET['voyageurs'] ?? '') === '1')  ? 'selected' : '' ?>>1 voyageur</option>
                        <option value="2"  <?= (($_GET['voyageurs'] ?? '') === '2')  ? 'selected' : '' ?>>2 voyageurs</option>
                        <option value="3"  <?= (($_GET['voyageurs'] ?? '') === '3')  ? 'selected' : '' ?>>3 voyageurs</option>
                        <option value="4"  <?= (($_GET['voyageurs'] ?? '') === '4')  ? 'selected' : '' ?>>4 voyageurs +</option>
                    </select>
                </div>
            </div>

            <div class="col-md-2 p-2">
                <button type="submit" class="btn btn-primary w-100 rounded-pill py-3 fw-bold shadow-sm" style="background-color: #4d7c6d; border: none;">
                    <i class="bi bi-search me-2"></i>Rechercher
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ===== Section "À la une" ===== -->
<section class="container my-5">
    <h3 class="fw-bold mb-4">À la une</h3>

    <?php if (empty($offres_une)): ?>
    <div class="rounded-4 p-5 text-center" style="background: linear-gradient(135deg, #f0f4f3 0%, #e8f0ee 100%); border: 2px dashed #c5d9d5;">
        <i class="bi bi-house-heart fs-1 mb-3" style="color: #4d7c6d;"></i>
        <h5 class="fw-bold text-dark mb-2">De beaux logements arrivent bientôt</h5>
        <p class="text-muted mb-3">Nos propriétaires préparent leurs offres. Revenez dans quelques jours pour découvrir nos premières annonces.</p>
        <a href="index.php?action=destinations" class="btn rounded-pill px-4 py-2 fw-bold" style="background-color: #4d7c6d; color:white; border:none;">
            <i class="bi bi-compass me-2"></i>Explorer les destinations
        </a>
    </div>
    <?php else: ?>
    <div class="row g-4">
        <?php foreach($offres_une as $o): ?>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 property-card">
                <img src="<?= htmlspecialchars($o['IMAGE_HABIT']) ?>" class="card-img-top" style="height: 200px; object-fit: cover;" alt="<?= htmlspecialchars($o['TITRE_HABIT']) ?>">
                <div class="card-body">
                    <h5 class="fw-bold mb-1"><?= htmlspecialchars($o['VILLE_HABIT']) ?></h5>
                    <p class="text-muted small"><?= (int)$o['NB_CHAMBRES_HABIT'] ?> pièces • Confort</p>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <span class="fw-bold text-dark"><?= (int)$o['PRIX_NUIT_HABIT'] ?>€ <small class="text-muted fw-normal">/nuit</small></span>
                        <a href="index.php?action=details&id_habit=<?= (int)$o['ID_HABIT'] ?>" class="btn btn-outline-dark btn-sm rounded-pill px-3">Détails</a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</section>

<!-- ===== Section régionale dynamique ===== -->
<section class="container mb-5">
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h3 class="fw-bold mb-0"><?= htmlspecialchars($nom_region_1) ?></h3>
            <p class="text-muted mb-0">Découvrez le charme authentique des Alpes</p>
        </div>
    </div>

    <?php if (empty($logements_region_1)): ?>
    <div class="rounded-4 p-5 text-center" style="background: linear-gradient(135deg, #faf7f2 0%, #f4ede0 100%); border: 2px dashed #d4bfa0;">
        <i class="bi bi-geo-alt fs-1 mb-3" style="color: #a0785a;"></i>
        <h5 class="fw-bold text-dark mb-2">Cette région prépare ses offres</h5>
        <p class="text-muted mb-3">Aucune habitation n'est encore disponible dans cette zone. D'autres destinations vous attendent dès maintenant.</p>
        <a href="index.php?action=destinations" class="btn rounded-pill px-4 py-2 fw-bold" style="background-color: #a0785a; color:white; border:none;">
            <i class="bi bi-map me-2"></i>Voir toutes les destinations
        </a>
    </div>
    <?php else: ?>
    <div class="row g-4">
        <?php foreach($logements_region_1 as $l): ?>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 property-card">
                <img src="<?= htmlspecialchars($l['IMAGE_HABIT']) ?>" class="card-img-top" style="height: 200px; object-fit: cover;" alt="<?= htmlspecialchars($l['TITRE_HABIT']) ?>">
                <div class="card-body">
                    <h5 class="fw-bold"><?= htmlspecialchars($l['VILLE_HABIT']) ?></h5>
                    <p class="text-muted mb-3"><?= (int)$l['NB_CHAMBRES_HABIT'] ?> pièces • Tout confort</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fs-5 fw-bold text-primary"><?= (int)$l['PRIX_NUIT_HABIT'] * 7 ?>€ <small class="text-muted fw-light" style="font-size: 0.7em;">/semaine</small></span>
                        <a href="index.php?action=details&id_habit=<?= (int)$l['ID_HABIT'] ?>" class="btn btn-dark btn-sm px-3 rounded-pill">Détails</a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        // Validation destination : lettres, espaces, tirets, apostrophes uniquement
        const destInput = document.getElementById('destination_input');
        if (destInput) {
            destInput.addEventListener('input', function() {
                // Supprime en temps réel tout caractère non alphabétique
                this.value = this.value.replace(/[^A-Za-zÀ-ÖØ-öø-ÿ\s\-']/g, '');
            });
        }

        flatpickr("#dates_sejour", {
            mode: "range",
            minDate: "today",
            dateFormat: "d/m/Y",
            locale: flatpickr.l10ns.fr,
            showMonths: 2,
            allowInput: false,
            // Force le calendrier à s'attacher au body pour éviter
            // qu'il passe sous la navbar sticky ou le filter-bar
            appendTo: document.body,
            onReady: function(selectedDates, dateStr, instance) {
                // Z-index supérieur à la navbar (sticky-top = 1020 sous Bootstrap)
                instance.calendarContainer.style.zIndex = "9999";
            },
            onOpen: function(selectedDates, dateStr, instance) {
                instance.calendarContainer.style.zIndex = "9999";
            },
            onClose: function(selectedDates, dateStr, instance) {
                // Rien à faire
            }
        });
    });
</script>