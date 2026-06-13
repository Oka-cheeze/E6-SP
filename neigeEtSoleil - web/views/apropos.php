<!-- ══════════════════════════════════════════
     HERO — même classe hero-section que style.css
══════════════════════════════════════════ -->
<section class="hero-section">
    <div class="container text-center">
        <h1 class="text-white fw-bold display-3">À propos de nous</h1>
        <p class="text-white fs-5 mt-3">Découvrez notre agence, notre équipe et les ressources du projet.</p>
    </div>
</section>


<!-- ══════════════════════════════════════════
     CONTENU PRINCIPAL
══════════════════════════════════════════ -->
<div class="container mt-5 pt-3 mb-5">

    <!-- Présentation -->
    <div class="text-center mb-5">
        <h2 class="fw-bold display-6">Neige et Soleil PPE</h2>
        <p class="text-muted fs-5 mt-2">
            Une agence de gestion locative spécialisée dans l'immobilier de montagne,<br>
            située dans la magnifique vallée du Queyras, près de Briançon.
        </p>
    </div>

    <!-- ── Équipe ── -->
    <h3 class="fw-bold mb-4">Notre équipe</h3>
    <div class="row g-3 mb-5">

        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 text-center">
                <div class="icon-circle mx-auto mb-3">
                    <i class="bi bi-person-badge fs-3"></i>
                </div>
                <h6 class="fw-bold mb-1">Directeur</h6>
                <p class="text-muted small mb-0">Pilotage global et négociation des contrats de mandat locatif.</p>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 text-center">
                <div class="icon-circle mx-auto mb-3">
                    <i class="bi bi-calculator fs-3"></i>
                </div>
                <h6 class="fw-bold mb-1">Secrétaire-Comptable</h6>
                <p class="text-muted small mb-0">Gestion administrative, relances et publipostage.</p>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 text-center">
                <div class="icon-circle mx-auto mb-3">
                    <i class="bi bi-people fs-3"></i>
                </div>
                <h6 class="fw-bold mb-1">2 Commerciaux</h6>
                <p class="text-muted small mb-0">Gestion des réservations et relation client au quotidien.</p>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 text-center">
                <div class="icon-circle mx-auto mb-3">
                    <i class="bi bi-tools fs-3"></i>
                </div>
                <h6 class="fw-bold mb-1">Ouvrier</h6>
                <p class="text-muted small mb-0">Entretien des appartements et maintenance du matériel.</p>
            </div>
        </div>

    </div>

    <!-- ── Ressources du projet ── -->
    <h3 class="fw-bold mb-4">Ressources du projet</h3>
    <div class="row g-4 mb-5">

        <!-- Fiche Contexte -->
        <div class="col-md-4">
            <div class="card destination-card border-0 shadow-sm rounded-4 h-100 p-4 text-center">
                <div class="icon-circle mx-auto mb-3">
                    <i class="bi bi-file-earmark-pdf fs-3"></i>
                </div>
                <h5 class="fw-bold mb-1">Notre contexte</h5>
                <p class="text-muted small">Présentation complète du contexte de l'entreprise et de son activité dans le Queyras.</p>
                <a href="contexte/ficheDeContexte.pdf" target="_blank"
                   class="btn btn-warning animate-float mt-auto fw-bold rounded-pill px-4">
                    <i class="bi bi-download me-1"></i>Voir le PDF
                </a>
            </div>
        </div>

        <!-- Cahier des charges -->
        <div class="col-md-4">
            <div class="card destination-card border-0 shadow-sm rounded-4 h-100 p-4 text-center">
                <div class="icon-circle mx-auto mb-3">
                    <i class="bi bi-journal-text fs-3"></i>
                </div>
                <h5 class="fw-bold mb-1">Cahier des charges</h5>
                <p class="text-muted small">Spécifications complètes : objectifs, besoins fonctionnels et contraintes du projet.</p>
                <a href="contexte/Cahier_des_charges_Neige_Soleil.pdf" target="_blank"
                   class="btn btn-warning animate-float mt-auto fw-bold rounded-pill px-4">
                    <i class="bi bi-file-text me-1"></i>Consulter
                </a>
            </div>
        </div>

        <!-- MCD -->
        <div class="col-md-4">
            <div class="card destination-card border-0 shadow-sm rounded-4 h-100 p-4 text-center">
                <div class="icon-circle mx-auto mb-3">
                    <i class="bi bi-diagram-3 fs-3"></i>
                </div>
                <h5 class="fw-bold mb-1">Schéma MCD</h5>
                <p class="text-muted small">Modèle Conceptuel de Données représentant la structure de notre base de données.</p>
                <a href="contexte/mcd.pdf" target="_blank"
                   class="btn btn-warning animate-float mt-auto fw-bold rounded-pill px-4">
                    <i class="bi bi-image me-1"></i>Découvrir
                </a>
            </div>
        </div>

    </div>

    <!-- ── Activités ── -->
    <h3 class="fw-bold mb-4">Nos activités</h3>
    <div class="row g-3 mb-5">

        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle bg-light p-3 me-3">
                        <i class="bi bi-house-door fs-4" style="color: #4d7c6d;"></i>
                    </div>
                    <h5 class="fw-bold mb-0">Location d'appartements</h5>
                </div>
                <p class="text-muted small mb-0">
                    Gestion de contrats de mandat locatif annuels avec les propriétaires, 
                    réservations saisonnières et édition d'un catalogue annuel diffusé aux 
                    anciens locataires et offices de tourisme.
                </p>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle bg-light p-3 me-3">
                        <i class="bi bi-geo-alt-fill fs-4" style="color: #4d7c6d;"></i>
                    </div>
                    <h5 class="fw-bold mb-0">Activités à proximité</h5>
                </div>
                <p class="text-muted small mb-0">
                    Découverte des activités disponibles autour de chaque logement : ski alpin,
                    randonnées, spa thermal, chiens de traîneau et bien plus encore.
                    Chaque fiche logement vous présente les activités proposées dans la station.
                </p>
            </div>
        </div>

    </div>

</div>