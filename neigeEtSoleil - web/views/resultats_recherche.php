<div class="container mt-5 pt-5" style="min-height: 80vh;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Résultats de recherche</h2>
            <p class="text-muted">
                <?php if (!empty($_GET['destination'])): ?>
                    Logements disponibles pour <strong><?= htmlspecialchars($_GET['destination']) ?></strong>
                <?php else: ?>
                    Tous les logements disponibles
                <?php endif; ?>
            </p>
        </div>
        <?php if (!empty($_GET['dates'])): ?>
        <span class="badge bg-light text-dark border p-2 px-3 rounded-pill">
            <i class="bi bi-calendar3 me-2 text-primary"></i>
            <?= htmlspecialchars($_GET['dates']) ?>
        </span>
        <?php endif; ?>
    </div>

    <?php if (empty($logements_trouves)): ?>
        <div class="rounded-4 p-5 text-center" style="background: linear-gradient(135deg, #f0f4f3 0%, #e8f0ee 100%); border: 2px dashed #c5d9d5;">
            <i class="bi bi-search fs-1 mb-3" style="color: #4d7c6d;"></i>
            <h5 class="fw-bold text-dark mb-2">Aucun logement trouvé</h5>
            <p class="text-muted mb-4">
                Aucun logement disponible ne correspond à votre recherche
                <?php if (!empty($_GET['destination'])): ?>
                    pour <strong>«&nbsp;<?= htmlspecialchars($_GET['destination']) ?>&nbsp;»</strong>
                <?php endif; ?>
                <?php if (!empty($_GET['voyageurs'])): ?>
                    pour <strong><?= (int)$_GET['voyageurs'] ?> voyageur(s)</strong>
                <?php endif; ?>.
            </p>
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="index.php" class="btn rounded-pill px-4 py-2 fw-bold" style="background-color: #4d7c6d; color:white; border:none;">
                    <i class="bi bi-arrow-left me-2"></i>Modifier la recherche
                </a>
                <a href="index.php?action=destinations" class="btn btn-outline-secondary rounded-pill px-4 py-2 fw-bold">
                    <i class="bi bi-map me-2"></i>Toutes les destinations
                </a>
            </div>
        </div>
    <?php else: ?>
        <p class="text-muted mb-4"><strong><?= count($logements_trouves) ?></strong> logement(s) trouvé(s)</p>
        <div class="row g-4">
            <?php foreach($logements_trouves as $l): 
                $galerie = $unModele->getPhotosByHabit($l['ID_HABIT']); 
            ?>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 overflow-hidden rounded-4">
                    <div id="carouselSearch<?= $l['ID_HABIT'] ?>" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <img src="<?= htmlspecialchars($l['IMAGE_HABIT'] ?? 'assets/img/locations/default.jpg') ?>"
                                    class="d-block w-100" style="height: 220px; object-fit: cover;"
                                    alt="<?= htmlspecialchars($l['TITRE_HABIT'] ?? '') ?>">
                            </div>
                            <?php foreach($galerie as $p): ?>
                                <div class="carousel-item">
                                    <img src="<?= htmlspecialchars($p['CHEMIN_PHOTO'] ?? '') ?>" class="d-block w-100" style="height: 220px; object-fit: cover;">
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php if(!empty($galerie)): ?>
                            <button class="carousel-control-prev" type="button" data-bs-target="#carouselSearch<?= $l['ID_HABIT'] ?>" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon"></span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#carouselSearch<?= $l['ID_HABIT'] ?>" data-bs-slide="next">
                                <span class="carousel-control-next-icon"></span>
                            </button>
                        <?php endif; ?>
                    </div>

                    <div class="card-body">
                        <h5 class="fw-bold"><?= htmlspecialchars($l['VILLE_HABIT'] ?? '') ?></h5>
                        <p class="text-muted small mb-1"><?= htmlspecialchars($l['TITRE_HABIT'] ?? '') ?></p>
                        <p class="text-muted mb-3"><?= (int)($l['NB_CHAMBRES_HABIT'] ?? 0) ?> pièces • Tout confort</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-dark">
                                <?= (int)($l['PRIX_NUIT_HABIT'] ?? 0) ?>€
                                <small class="text-muted fw-normal">/nuit</small>
                            </span>
                            <a href="index.php?action=details&id_habit=<?= (int)$l['ID_HABIT'] ?>&dates_sejour=<?= urlencode($_GET['dates'] ?? '') ?>"
                            class="btn btn-dark btn-sm px-4 rounded-pill">
                                Voir l'offre
                            </a>
                        </div>
                    </div>
                </div>
            </div>
    <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="mt-5 border-top pt-4">
        <a href="index.php" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i>Retour à l'accueil
        </a>
    </div>
</div>