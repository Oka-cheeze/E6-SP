<div class="container mt-5 pt-5 mb-5">
    <div class="text-center mb-5">
        <h1 class="fw-bold display-5">Nos Destinations</h1>
        <p class="text-muted fs-5">Trouvez le massif qui correspond à vos envies de neige.</p>
    </div>

    <?php if (isset($_GET['id_station'])): ?>
        <div class="mb-4">
            <a href="index.php?action=destinations" class="btn btn-outline-secondary rounded-pill mb-3">
                <i class="bi bi-arrow-left me-2"></i>Retour aux destinations
            </a>
            <h3 class="fw-bold">Logements disponibles</h3>
        </div>

        <?php if (empty($habitations)): ?>
        <div class="rounded-4 p-5 text-center mb-5" style="background: linear-gradient(135deg, #f0f4f3 0%, #e8f0ee 100%); border: 2px dashed #c5d9d5;">
            <i class="bi bi-house-slash fs-1 mb-3" style="color: #4d7c6d;"></i>
            <h5 class="fw-bold text-dark mb-2">Aucun logement disponible dans cette station</h5>
            <p class="text-muted mb-4">Les logements de cette station sont en cours de validation ou temporairement indisponibles.<br>Explorez une autre station ou revenez bientôt.</p>
            <a href="index.php?action=destinations" class="btn rounded-pill px-4 py-2 fw-bold" style="background-color: #4d7c6d; color:white; border:none;">
                <i class="bi bi-map me-2"></i>Voir toutes les destinations
            </a>
        </div>
        <?php else: ?>
        <div class="row g-4 mb-5">
            <?php foreach($habitations as $h):
                $galerie = $unModele->getPhotosByHabit($h['ID_HABIT']);
            ?>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 overflow-hidden rounded-4">
                    <div id="carouselDest<?= $h['ID_HABIT'] ?>" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <img src="<?= htmlspecialchars($h['IMAGE_HABIT'] ?? 'assets/img/locations/default.jpg') ?>"
                                    class="d-block w-100" style="height: 220px; object-fit: cover;"
                                    alt="<?= htmlspecialchars($h['TITRE_HABIT'] ?? '') ?>">
                            </div>
                            <?php foreach($galerie as $p): ?>
                                <div class="carousel-item">
                                    <img src="<?= htmlspecialchars(htmlspecialchars($p['CHEMIN_PHOTO'] ?? '')) ?>" class="d-block w-100" style="height: 220px; object-fit: cover;">
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php if(!empty($galerie)): ?>
                            <button class="carousel-control-prev" type="button" data-bs-target="#carouselDest<?= $h['ID_HABIT'] ?>" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon"></span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#carouselDest<?= $h['ID_HABIT'] ?>" data-bs-slide="next">
                                <span class="carousel-control-next-icon"></span>
                            </button>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <h5 class="fw-bold"><?= htmlspecialchars($h['TITRE_HABIT'] ?? $h['VILLE_HABIT']) ?></h5>
                        <p class="text-muted small mb-3">
                            <i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($h['VILLE_HABIT']) ?>
                            <span class="mx-2">&bull;</span>
                            <?= (int)$h['NB_CHAMBRES_HABIT'] ?> pièces
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-primary"><?= (int)$h['PRIX_NUIT_HABIT'] ?>€ <small class="text-muted">/nuit</small></span>
                            <a href="index.php?action=details&id_habit=<?= (int)$h['ID_HABIT'] ?>" class="btn btn-dark btn-sm px-4 rounded-pill">
                                Voir l'offre
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        <hr class="my-5">
    <?php endif; ?>

    <div class="row g-4">
        <?php foreach($regions as $reg):
            $icon = "bi-snow";
            if (strpos($reg['NOM_REG'], 'Alpes')    !== false) $icon = "bi-mountain-fill";
            if (strpos($reg['NOM_REG'], 'Jura')     !== false) $icon = "bi-tree-fill";
            if (strpos($reg['NOM_REG'], 'Pyrénées') !== false) $icon = "bi-clouds-fill";
        ?>
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle bg-light p-3 me-3">
                        <i class="bi <?= $icon ?> fs-3" style="color: #4d7c6d;"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold h4 mb-0"><?= htmlspecialchars($reg['NOM_REG']) ?></h3>
                        <small class="text-muted">Département <?= htmlspecialchars($reg['DEP_REG']) ?></small>
                    </div>
                </div>
                <div class="list-group list-group-flush mt-3">
                    <?php
                    $compteur = 0;
                    foreach($stations as $stat):
                        if ($stat['ID_REG'] == $reg['ID_REG']):
                            $compteur++;
                    ?>
                    <a href="index.php?action=destinations&id_station=<?= (int)$stat['ID_STATION'] ?>"
                       class="list-group-item list-group-item-action station-link border-0 d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-geo-alt me-2 opacity-50"></i><?= htmlspecialchars($stat['NOM_STATION']) ?></span>
                        <span class="badge rounded-pill px-3" style="background-color: #4d7c6d;">Explorer</span>
                    </a>
                    <?php
                        endif;
                    endforeach;
                    if ($compteur == 0): ?>
                        <p class="text-muted small fst-italic">Aucune station disponible pour le moment.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>