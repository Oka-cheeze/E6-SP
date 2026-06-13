<div class="container mt-5 pt-5 mb-5">
    <?php if (isset($_GET['status'])): ?>
        <div class="alert alert-<?= ($_GET['status'] == 'valide') ? 'success' : 'danger' ?> border-0 shadow-sm mb-4">
            Le contrat a été <strong><?= ($_GET['status'] == 'valide') ? 'validé' : 'rejeté' ?></strong>.
        </div>
    <?php endif; ?>

    <h2 class="fw-bold mb-4">Gestion des contrats en attente</h2>

    <?php if (empty($en_attente)): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-check-circle fs-1 text-success mb-3 d-block"></i>
            Aucun contrat en attente de validation.
        </div>
    <?php else: ?>

    <!-- Tableau -->
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Logement</th>
                    <th>Prix/Nuit</th>
                    <th class="text-end pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($en_attente as $h):
                    $equips = $unModele->getEquipementsByHabit($h['ID_HABIT']);
                    $photos = $unModele->getPhotosByHabit($h['ID_HABIT']);
                ?>
                <tr>
                    <td class="ps-4">
                        <div class="d-flex align-items-center">
                            <img src="<?= htmlspecialchars($h['IMAGE_HABIT'] ?? '') ?>"
                                 class="rounded-3 me-3" width="60" height="45" style="object-fit: cover;">
                            <div>
                                <div class="fw-bold"><?= htmlspecialchars($h['TITRE_HABIT']) ?></div>
                                <div class="text-muted small"><?= htmlspecialchars($h['VILLE_HABIT']) ?></div>
                            </div>
                        </div>
                    </td>
                    <td><span class="fw-bold text-primary"><?= $h['PRIX_NUIT_HABIT'] ?>€</span></td>
                    <td class="text-end pe-4">
                        <button class="btn btn-sm btn-outline-primary rounded-pill px-3 me-2"
                                data-bs-toggle="modal" data-bs-target="#modalDetail<?= $h['ID_HABIT'] ?>">
                            <i class="bi bi-eye me-1"></i> Détails
                        </button>
                        <a href="index.php?action=valider_bien&id_habit=<?= $h['ID_HABIT'] ?>"
                           class="btn btn-sm btn-success rounded-pill px-3 me-2">
                            <i class="bi bi-check-lg"></i>
                        </a>
                        <button onclick="demanderMotif(<?= $h['ID_HABIT'] ?>)"
                                class="btn btn-sm btn-danger rounded-pill px-3">
                            <i class="bi bi-x-lg"></i>
                        </button>

                        <!-- Formulaire caché pour le refus -->
                        <form id="refus-<?= $h['ID_HABIT'] ?>" action="index.php?action=rejeter_contrat"
                              method="POST" style="display:none;">
                            <input type="hidden" name="id_habit" value="<?= $h['ID_HABIT'] ?>">
                            <input type="hidden" name="motif" id="input-motif-<?= $h['ID_HABIT'] ?>">
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Modales HORS du tableau (HTML valide) -->
    <?php foreach($en_attente as $h):
        $equips = $unModele->getEquipementsByHabit($h['ID_HABIT']);
        $photos = $unModele->getPhotosByHabit($h['ID_HABIT']);
    ?>
    <div class="modal fade" id="modalDetail<?= $h['ID_HABIT'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content rounded-4 border-0">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Étude du dossier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div id="carouselAdmin<?= $h['ID_HABIT'] ?>"
                                 class="carousel slide shadow-sm rounded-4 overflow-hidden mb-3"
                                 data-bs-ride="carousel">
                                <div class="carousel-inner">
                                    <div class="carousel-item active">
                                        <img src="<?= htmlspecialchars($h['IMAGE_HABIT'] ?? '') ?>"
                                             class="d-block w-100" style="height:250px; object-fit:cover;">
                                    </div>
                                    <?php foreach($photos as $p): ?>
                                        <div class="carousel-item">
                                            <img src="<?= htmlspecialchars(htmlspecialchars($p['CHEMIN_PHOTO'] ?? '')) ?>"
                                                 class="d-block w-100" style="height:250px; object-fit:cover;">
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php if(!empty($photos)): ?>
                                    <button class="carousel-control-prev" type="button"
                                            data-bs-target="#carouselAdmin<?= $h['ID_HABIT'] ?>" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon"></span>
                                    </button>
                                    <button class="carousel-control-next" type="button"
                                            data-bs-target="#carouselAdmin<?= $h['ID_HABIT'] ?>" data-bs-slide="next">
                                        <span class="carousel-control-next-icon"></span>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h4 class="fw-bold text-primary mb-1"><?= htmlspecialchars($h['TITRE_HABIT']) ?></h4>
                            <p class="text-muted small">
                                <i class="bi bi-geo-alt me-1"></i>
                                <?= htmlspecialchars($h['ADRESSE_HABIT'] ?? '') ?>,
                                <?= htmlspecialchars($h['CP_HABIT'] ?? '') ?>
                                <?= htmlspecialchars($h['VILLE_HABIT'] ?? '') ?>
                            </p>
                            <hr>
                            <div class="row g-2 mb-3 text-center">
                                <div class="col-4">
                                    <div class="p-2 border rounded-3 small">
                                        <strong>Surface</strong><br><?= $h['SURFACE_HABIT'] ?>m²
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="p-2 border rounded-3 small">
                                        <strong>Voyageurs</strong><br><?= $h['NB_GUETS_HABIT'] ?>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="p-2 border rounded-3 small">
                                        <strong>Chambres</strong><br><?= $h['NB_CHAMBRES_HABIT'] ?>
                                    </div>
                                </div>
                            </div>
                            <h6><strong>Équipements :</strong></h6>
                            <div class="d-flex flex-wrap gap-1">
                                <?php if(!empty($equips)): foreach($equips as $e): ?>
                                    <span class="badge bg-white text-dark border rounded-pill fw-normal px-2 py-1">
                                        <i class="bi bi-check2 text-success"></i>
                                        <?= htmlspecialchars($e['NOM_EQUIP']) ?>
                                    </span>
                                <?php endforeach; else: ?>
                                    <span class="text-muted small fst-italic">Aucun équipement renseigné.</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <?php endif; ?>
</div>

<script>
function demanderMotif(id) {
    let m = prompt("Motif du refus :");
    if (m) {
        document.getElementById('input-motif-' + id).value = m;
        document.getElementById('refus-' + id).submit();
    }
}
</script>