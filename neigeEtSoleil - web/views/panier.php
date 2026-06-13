<?php
// Initialiser la structure si nécessaire
if (!isset($_SESSION['panier']['reservations'])) {
    $_SESSION['panier']['reservations'] = [];
}

$reservations = $_SESSION['panier']['reservations'];
$total_general = 0;
?>

<div class="container mt-5 pt-4 mb-5">
    <h2 class="fw-bold mb-4"><i class="bi bi-calendar-check me-2"></i>Mes Réservations</h2>
    
    <?php if (empty($reservations)): ?>
        <!-- Panier vide -->
        <div class="text-center py-5 shadow-sm rounded-4 bg-white border">
            <div class="mb-4">
                <i class="bi bi-calendar-x text-muted" style="font-size: 4rem;"></i>
            </div>
            <h3 class="fw-bold">Votre panier est vide</h3>
            <p class="text-muted mb-4">Vous n'avez pas encore sélectionné de logement pour votre séjour.</p>
            <a href="index.php?action=destinations" class="btn btn-primary btn-lg rounded-pill px-5 shadow-sm" style="background-color: #4d7c6d; border:none;">
                <i class="bi bi-house me-2"></i>Voir les destinations
            </a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <div class="col-lg-8">
                <?php foreach ($reservations as $index => $resa): 
                    // Calcul du prix pour cette réservation
                    $date_debut = new DateTime($resa['date_debut']);
                    $date_fin = new DateTime($resa['date_fin']);
                    $nb_nuits = $date_debut->diff($date_fin)->days;
                    $prix_logement = $nb_nuits * $resa['logement']['PRIX_NUIT_HABIT'];
                    
                    $total_reservation = $prix_logement;
                    $total_general += $total_reservation;
                ?>
                
                <!-- Carte de réservation -->
                <div class="card mb-4 rounded-4 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                            <h5 class="fw-bold mb-0">
                                <i class="bi bi-house-door me-2 text-primary"></i>Réservation #<?= $index + 1 ?>
                            </h5>
                            <span class="badge bg-primary-subtle text-primary">
                                Total : <?= number_format($total_reservation, 2) ?> €
                            </span>
                        </div>
                        
                        <!-- Hébergement -->
                        <div class="row align-items-center mb-3">
                            <div class="col-md-3">
                                <?php 
                                    $id_h = $resa['logement']['ID_HABIT'];
                                    $galerie = $unModele->getPhotosByHabit($id_h); 
                                ?>
                                <div id="carouselPanier<?= $id_h ?>" class="carousel slide rounded-3 overflow-hidden shadow-sm" data-bs-ride="carousel">
                                    <div class="carousel-inner">
                                        <div class="carousel-item active">
                                            <img src="<?= $resa['logement']['IMAGE_HABIT'] ?? 'assets/img/locations/default.jpg' ?>" 
                                                class="d-block w-100" style="height: 150px; object-fit: cover;" alt="Logement">
                                        </div>
                                        <?php foreach($galerie as $p): ?>
                                            <div class="carousel-item">
                                                <img src="<?= htmlspecialchars($p['CHEMIN_PHOTO'] ?? '') ?>" class="d-block w-100" style="height: 150px; object-fit: cover;">
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php if(!empty($galerie)): ?>
                                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselPanier<?= $id_h ?>" data-bs-slide="prev">
                                            <span class="carousel-control-prev-icon" style="width: 1.2rem; height: 1.2rem;"></span>
                                        </button>
                                        <button class="carousel-control-next" type="button" data-bs-target="#carouselPanier<?= $id_h ?>" data-bs-slide="next">
                                            <span class="carousel-control-next-icon" style="width: 1.2rem; height: 1.2rem;"></span>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <h6 class="fw-bold mb-2"><?= htmlspecialchars($resa['logement']['TITRE_HABIT'] ?? 'Logement') ?></h6>
                                <p class="text-muted small mb-1">
                                    <i class="bi bi-geo-alt me-1"></i><?= $resa['logement']['VILLE_HABIT'] ?? '' ?>
                                </p>
                                <p class="text-muted small mb-1">
                                    <i class="bi bi-calendar-range me-1"></i>
                                    <?= date('d/m/Y', strtotime($resa['date_debut'])) ?> 
                                    au <?= date('d/m/Y', strtotime($resa['date_fin'])) ?>
                                </p>
                                <p class="text-muted small mb-0">
                                    <i class="bi bi-moon-stars me-1"></i><?= $nb_nuits ?> nuit<?= $nb_nuits > 1 ? 's' : '' ?> 
                                    × <?= $resa['logement']['PRIX_NUIT_HABIT'] ?>€ = <strong><?= number_format($prix_logement, 2) ?>€</strong>
                                </p>
                            </div>
                            <div class="col-md-2 text-end">
                                <a href="index.php?action=supprimer_reservation&index=<?= $index ?>" 
                                   class="btn btn-sm btn-outline-danger rounded-pill px-3"
                                   onclick="return confirm('Supprimer cette réservation ?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </div>
                        

                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Récapitulatif global -->
            <div class="col-lg-4">
                <div class="card rounded-4 border-0 shadow-sm p-4 sticky-top" style="top: 100px;">
                    <h5 class="fw-bold mb-4">Récapitulatif</h5>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Nombre de réservations</span>
                            <span class="fw-bold"><?= count($reservations) ?></span>
                        </div>
                    </div>
                    
                    <hr class="my-3">
                    
                    <div class="d-flex justify-content-between mb-4">
                        <span class="fw-bold fs-5">Total</span>
                        <span class="fw-bold fs-5 text-primary"><?= number_format($total_general, 2) ?> €</span>
                    </div>
                    
                    <?php if (isset($_SESSION['id_user'])): ?>
                        <a href="index.php?action=confirmer_reservations" class="btn btn-primary w-100 rounded-pill py-3 fw-bold shadow-sm" style="background-color: #4d7c6d; border:none;">
                            <i class="bi bi-check-circle me-2"></i>Confirmer toutes les réservations
                        </a>
                    <?php else: ?>
                        <a href="index.php?action=connexion" class="btn btn-primary w-100 rounded-pill py-3 fw-bold shadow-sm" style="background-color: #4d7c6d; border:none;">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Se connecter pour réserver
                        </a>
                        <p class="text-center text-muted small mt-3 mb-0">Vous devez être connecté pour confirmer</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>