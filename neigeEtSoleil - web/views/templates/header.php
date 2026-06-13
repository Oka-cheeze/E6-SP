<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Neige et Soleil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_green.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/fr.js"></script>
    <style>
        /* Force le calendrier flatpickr à passer au-dessus de tout */
        .flatpickr-calendar { z-index: 9999 !important; }
    </style>
</head>
<body>

<?php if (isset($_SESSION['flash_success'])): ?>
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 2000;">
        <div id="liveToast" class="toast show align-items-center text-white bg-success border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    <?= $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['flash_error'])): ?>
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 2000;">
        <div id="liveToastError" class="toast show align-items-center text-white bg-danger border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    <?= $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>
<?php endif; ?>

<nav class="navbar navbar-expand-lg navbar-light bg-white py-3 shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold fs-4 text-primary" href="index.php?action=accueil">
            <i class="bi bi-sun-fill me-2"></i>Neige et Soleil
        </a>
        
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link px-3 fw-medium" href="index.php?action=accueil">Accueil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 fw-medium" href="index.php?action=destinations">Destinations</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 fw-medium" href="index.php?action=apropos">À propos</a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-3">
                <a href="index.php?action=panier" class="btn btn-outline-dark position-relative rounded-pill px-3 shadow-sm">
                    <i class="bi bi-calendar-check me-1"></i>
                    <span class="d-none d-md-inline">Mes réservations</span>
                    
                    <?php 
                    $count = 0;
                    if (isset($_SESSION['panier'])) {
                        // On compte 1 si un logement est présent
                        if (!empty($_SESSION['panier']['logement'])) $count++;
                        // On ajoute le nombre d'activités
                        if (!empty($_SESSION['panier']['activites'])) $count += count($_SESSION['panier']['activites']);
                    }
                    
                    if ($count > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            <?= $count ?>
                        </span>
                    <?php endif; ?>
                </a>

                <div class="ms-2">
                    <?php if (isset($_SESSION['id_user'])): ?>
                        <div class="dropdown">
                            <button class="btn btn-light rounded-pill px-3 py-2 d-flex align-items-center gap-2 border shadow-sm" type="button" data-bs-toggle="dropdown">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; font-size: 12px;">
                                    <?= strtoupper(substr($_SESSION['prenom'], 0, 1)) ?>
                                </div>
                                <span class="fw-medium"><?= $_SESSION['prenom'] ?></span>
                                <i class="bi bi-chevron-down small"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end rounded-4 shadow border-0 mt-2 p-2">
                                <li><a class="dropdown-item" href="index.php?action=profile"><i class="bi bi-person me-2"></i>Mon Profil</a></li>
                                
                                <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'proprietaire'): ?>
                                    <li><a class="dropdown-item" href="index.php?action=dashboard"><i class="bi bi-speedometer2 me-2"></i>Tableau de bord</a></li>
                                <?php endif; ?>

                                <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                    <li><a class="dropdown-item" href="index.php?action=dashboard_admin"><i class="bi bi-speedometer2 me-2"></i>Tableau de bord Admin</a></li>
                                <?php endif; ?>
                                
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="index.php?action=logout"><i class="bi bi-box-arrow-right me-2"></i>Déconnexion</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="index.php?action=connexion" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm" 
                        style="background-color: #4d7c6d; border: none;">
                            Connexion
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</nav>
<main class="flex-grow-1">