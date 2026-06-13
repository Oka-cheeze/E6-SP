<div class="container mt-5 pt-4 mb-5">

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                <div>
                    <?php switch($_GET['msg']) {
                        case 'supprime': echo "L'annonce a été supprimée avec succès."; break;
                        case 'modifie':  echo "Vos modifications ont été soumises et sont en attente de validation."; break;
                        case 'ajoute':   echo "Votre annonce est en attente de validation par l'administrateur."; break;
                    } ?>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-5">
        <h1 class="fw-bold">Mon Espace Propriétaire</h1>
        <button class="btn btn-primary rounded-pill px-4"
                data-bs-toggle="modal" data-bs-target="#addPropertyModal"
                onclick="reinitialiserModalePourAjout()"
                style="background-color: #4d7c6d; border:none;">
            <i class="bi bi-plus-lg me-2"></i>Ajouter un bien
        </button>
    </div>

    <!-- Grille des annonces -->
    <div class="row g-4">
        <?php if (!empty($mes_annonces)): ?>
            <?php foreach ($mes_annonces as $h): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">

                    <!-- Image principale + badge statut -->
                    <div class="position-relative">
                        <img src="<?= htmlspecialchars($h['IMAGE_HABIT'] ?? 'assets/img/locations/default.jpg') ?>"
                             class="card-img-top" style="height: 200px; object-fit: cover;"
                             alt="<?= htmlspecialchars($h['TITRE_HABIT']) ?>">
                        <div class="position-absolute top-0 end-0 m-2">
                            <?php if ($h['STATUT_HABIT'] === 'disponible'): ?>
                                <span class="badge bg-success rounded-pill shadow-sm">
                                    <i class="bi bi-check-lg me-1"></i>Validée
                                </span>
                            <?php elseif ($h['STATUT_HABIT'] === 'rejete'): ?>
                                <span class="badge bg-danger rounded-pill shadow-sm">
                                    <i class="bi bi-x-circle me-1"></i>Refusée
                                </span>
                            <?php elseif ($h['STATUT_HABIT'] === 'en_attente'): ?>
                                <span class="badge bg-warning text-dark rounded-pill shadow-sm">
                                    <i class="bi bi-clock-history me-1"></i>En attente
                                </span>
                            <?php else: ?>
                                <span class="badge bg-secondary rounded-pill shadow-sm">
                                    <i class="bi bi-question-circle me-1"></i>Statut inconnu
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card-body d-flex flex-column">
                        <h5 class="fw-bold mb-1"><?= htmlspecialchars($h['TITRE_HABIT']) ?></h5>
                        <p class="text-muted small mb-1">
                            <i class="bi bi-geo-alt me-1"></i>
                            <?= htmlspecialchars($h['VILLE_HABIT']) ?>
                            <?php if($h['CP_HABIT']): ?>(<?= htmlspecialchars($h['CP_HABIT']) ?>)<?php endif; ?>
                        </p>
                        <p class="text-muted small mb-3">
                            <i class="bi bi-house me-1"></i><?= (int)$h['SURFACE_HABIT'] ?>m²
                            &bull; <?= (int)$h['NB_CHAMBRES_HABIT'] ?> ch.
                            &bull; <?= (int)$h['NB_GUETS_HABIT'] ?> pers. max
                        </p>

                        <?php if ($h['STATUT_HABIT'] === 'rejete'): ?>
                            <?php $dernierContrat = $unModele->selectDernierContratByHabit($h['ID_HABIT']); ?>
                            <div class="alert alert-danger border-0 small py-2 mb-3">
                                <strong><i class="bi bi-exclamation-octagon-fill me-1"></i>Motif du refus :</strong><br>
                                <?= htmlspecialchars($dernierContrat['MOTIF_REFUS_CONTRAT'] ?? 'Motif non précisé.') ?>
                                <p class="mb-0 mt-1 text-dark" style="font-size:0.75rem;">
                                    Corrigez ces points et soumettez une nouvelle demande.
                                </p>
                            </div>
                        <?php endif; ?>

                        <div class="mt-auto d-flex gap-2">
                            <!-- Bouton Détails -->
                            <button class="btn btn-outline-secondary btn-sm rounded-pill flex-grow-1"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalApercu<?= $h['ID_HABIT'] ?>">
                                <i class="bi bi-eye me-1"></i>Détails
                            </button>

                            <!-- Bouton Modifier (désactivé si en_attente) -->
                            <?php if ($h['STATUT_HABIT'] !== 'en_attente'): ?>
                                <button class="btn btn-sm rounded-pill flex-grow-1"
                                        style="background-color: #4d7c6d; color:white; border:none;"
                                        onclick='remplirModaleEdit(<?= json_encode($h) ?>)'
                                        data-bs-toggle="modal" data-bs-target="#addPropertyModal">
                                    <i class="bi bi-pencil me-1"></i>Modifier
                                </button>
                            <?php else: ?>
                                <button class="btn btn-light btn-sm rounded-pill flex-grow-1 text-muted" disabled>
                                    <i class="bi bi-lock me-1"></i>En lecture...
                                </button>
                            <?php endif; ?>

                            <!-- Bouton Supprimer -->
                            <form action="index.php?action=supprimer_logement" method="POST"
                                  onsubmit="return confirm('Supprimer définitivement cette annonce ?')">
                                <input type="hidden" name="id_suppr" value="<?= $h['ID_HABIT'] ?>">
                                <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-2">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <i class="bi bi-house-slash fs-1 text-muted mb-3 d-block"></i>
                <p class="text-muted">Vous n'avez pas encore d'annonce.<br>Cliquez sur "Ajouter un bien" pour commencer.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- ===== MODALES DÉTAILS (hors de la grille) ===== -->
    <?php if (!empty($mes_annonces)): ?>
        <?php foreach ($mes_annonces as $h):
            $galerie      = $unModele->getPhotosByHabit($h['ID_HABIT']);
            $equipements  = $unModele->getEquipementsByHabit($h['ID_HABIT']);
        ?>
        <div class="modal fade" id="modalApercu<?= $h['ID_HABIT'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow">
                    <div class="modal-header border-0">
                        <h5 class="modal-title fw-bold">
                            <?= htmlspecialchars($h['TITRE_HABIT']) ?>
                            &nbsp;
                            <?php if ($h['STATUT_HABIT'] === 'disponible'): ?>
                                <span class="badge bg-success fs-6">Validée</span>
                            <?php elseif ($h['STATUT_HABIT'] === 'rejete'): ?>
                                <span class="badge bg-danger fs-6">Refusée</span>
                            <?php elseif ($h['STATUT_HABIT'] === 'en_attente'): ?>
                                <span class="badge bg-warning text-dark fs-6">En attente</span>
                            <?php else: ?>
                                <span class="badge bg-secondary fs-6">Statut inconnu</span>
                            <?php endif; ?>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-4">
                            <!-- Colonne gauche : carousel -->
                            <div class="col-md-6">
                                <div id="carouselOwner<?= $h['ID_HABIT'] ?>"
                                     class="carousel slide rounded-4 overflow-hidden shadow-sm"
                                     data-bs-ride="carousel">
                                    <div class="carousel-inner">
                                        <div class="carousel-item active">
                                            <img src="<?= htmlspecialchars($h['IMAGE_HABIT'] ?? '') ?>"
                                                 class="d-block w-100" style="height:260px; object-fit:cover;">
                                        </div>
                                        <?php foreach($galerie as $gp): ?>
                                            <div class="carousel-item">
                                                <img src="<?= htmlspecialchars($gp['CHEMIN_PHOTO'] ?? '') ?>"
                                                     class="d-block w-100" style="height:260px; object-fit:cover;">
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php if(!empty($galerie)): ?>
                                        <button class="carousel-control-prev" type="button"
                                                data-bs-target="#carouselOwner<?= $h['ID_HABIT'] ?>" data-bs-slide="prev">
                                            <span class="carousel-control-prev-icon"></span>
                                        </button>
                                        <button class="carousel-control-next" type="button"
                                                data-bs-target="#carouselOwner<?= $h['ID_HABIT'] ?>" data-bs-slide="next">
                                            <span class="carousel-control-next-icon"></span>
                                        </button>
                                    <?php endif; ?>
                                </div>
                                <p class="text-muted small mt-2 text-center">
                                    <?= 1 + count($galerie) ?> photo<?= count($galerie) > 0 ? 's' : '' ?>
                                </p>
                            </div>

                            <!-- Colonne droite : infos -->
                            <div class="col-md-6">
                                <p class="text-muted small mb-2">
                                    <i class="bi bi-geo-alt me-1"></i>
                                    <?= htmlspecialchars($h['ADRESSE_HABIT'] ?? '') ?>,
                                    <?= htmlspecialchars($h['CP_HABIT'] ?? '') ?>
                                    <?= htmlspecialchars($h['VILLE_HABIT'] ?? '') ?>
                                </p>
                                <hr class="my-2">
                                <div class="row g-2 text-center mb-3">
                                    <div class="col-4">
                                        <div class="p-2 border rounded-3 small">
                                            <strong>Surface</strong><br><?= $h['SURFACE_HABIT'] ?>m²
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="p-2 border rounded-3 small">
                                            <strong>Lits</strong><br><?= $h['NB_LITS_HABIT'] ?>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="p-2 border rounded-3 small">
                                            <strong>Capacité</strong><br><?= $h['NB_GUETS_HABIT'] ?> pers.
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-2 text-center mb-3">
                                    <div class="col-6">
                                        <div class="p-2 border rounded-3 small">
                                            <strong>Chambres</strong><br><?= $h['NB_CHAMBRES_HABIT'] ?>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="p-2 border rounded-3 small">
                                            <strong>Prix/nuit</strong><br><?= number_format($h['PRIX_NUIT_HABIT'], 0) ?>€
                                        </div>
                                    </div>
                                </div>

                                <!-- Équipements -->
                                <h6 class="fw-bold mb-2">Équipements</h6>
                                <div class="d-flex flex-wrap gap-1 mb-3">
                                    <?php if(!empty($equipements)): foreach($equipements as $e): ?>
                                        <span class="badge bg-light text-dark border rounded-pill fw-normal px-2 py-1">
                                            <i class="bi bi-check2 text-success"></i>
                                            <?= htmlspecialchars($e['NOM_EQUIP']) ?>
                                        </span>
                                    <?php endforeach; else: ?>
                                        <span class="text-muted small fst-italic">Aucun équipement renseigné.</span>
                                    <?php endif; ?>
                                </div>

                                <!-- Motif de refus si applicable -->
                                <?php if ($h['STATUT_HABIT'] === 'rejete'):
                                    $dernierContrat = $unModele->selectDernierContratByHabit($h['ID_HABIT']); ?>
                                    <div class="alert alert-danger border-0 small py-2">
                                        <strong><i class="bi bi-exclamation-octagon-fill me-1"></i>Motif du refus :</strong><br>
                                        <?= htmlspecialchars($dernierContrat['MOTIF_REFUS_CONTRAT'] ?? 'Non précisé') ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <?php if ($h['STATUT_HABIT'] !== 'en_attente'): ?>
                            <button class="btn btn-sm rounded-pill px-4"
                                    style="background-color: #4d7c6d; color:white; border:none;"
                                    data-bs-dismiss="modal"
                                    onclick='remplirModaleEdit(<?= json_encode($h) ?>)'
                                    data-bs-toggle="modal" data-bs-target="#addPropertyModal">
                                <i class="bi bi-pencil me-1"></i>Modifier cette annonce
                            </button>
                        <?php endif; ?>
                        <button class="btn btn-light btn-sm rounded-pill px-4" data-bs-dismiss="modal">Fermer</button>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- ===== MODALE AJOUT / MODIFICATION ===== -->
    <div class="modal fade" id="addPropertyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold" id="modalTitle">Ajouter un bien</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form method="post" enctype="multipart/form-data" action="index.php?action=ajouter_logement" id="formLogement">
                        <?php include("views/vue_gestion_habitation.php"); ?>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
function reinitialiserModalePourAjout() {
    document.getElementById('modalTitle').textContent = "Ajouter un bien";
    document.getElementById('btn-valider-modal').textContent = "Ajouter au Parc";
    document.getElementById('formLogement').action = "index.php?action=ajouter_logement";
    document.getElementById('formLogement').reset();
    // Supprimer l'éventuel champ id_habit caché
    const old = document.querySelector('#formLogement [name="id_habit"]');
    if (old) old.remove();
}

function remplirModaleEdit(h) {
    document.getElementById('modalTitle').textContent = "Modifier l'annonce";
    document.getElementById('btn-valider-modal').textContent = "Soumettre les modifications";
    document.getElementById('formLogement').action = "index.php?action=modifier_logement";

    const form = document.getElementById('formLogement');
    form.querySelector('[name="titre"]').value    = h.TITRE_HABIT    || "";
    form.querySelector('[name="ville"]').value    = h.VILLE_HABIT    || "";
    form.querySelector('[name="cp"]').value       = h.CP_HABIT       || "";
    form.querySelector('[name="adresse"]').value  = h.ADRESSE_HABIT  || "";
    form.querySelector('[name="prix"]').value     = h.PRIX_NUIT_HABIT || 0;
    form.querySelector('[name="surface"]').value  = h.SURFACE_HABIT  || 0;
    form.querySelector('[name="nb_c"]').value     = h.NB_CHAMBRES_HABIT || 0;
    form.querySelector('[name="beds"]').value     = h.NB_LITS_HABIT  || 0;
    form.querySelector('[name="guests"]').value   = h.NB_GUETS_HABIT || 0;

    // Champ id_habit caché (créé si absent)
    let inputId = form.querySelector('[name="id_habit"]');
    if (!inputId) {
        inputId = document.createElement('input');
        inputId.type  = 'hidden';
        inputId.name  = 'id_habit';
        form.appendChild(inputId);
    }
    inputId.value = h.ID_HABIT;
}
</script>