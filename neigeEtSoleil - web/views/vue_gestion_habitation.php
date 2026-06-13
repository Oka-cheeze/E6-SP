<form method="post" enctype="multipart/form-data">
    <input type="hidden" name="id_proprio" value="<?= $_SESSION['id_user'] ?? '' ?>">

    <div class="row g-3">
        <div class="col-md-8">
            <label class="small fw-bold text-muted mb-1">Titre de l'annonce</label>
            <input type="text" name="titre" class="form-control border-0 bg-light rounded-3" placeholder="Ex: Chalet avec vue panoramique" required>
        </div>
        <div class="col-md-4">
            <label class="small fw-bold text-muted mb-1">Type de bien</label>
            <select name="type_bien" id="type_bien" class="form-select border-0 bg-light rounded-3" onchange="afficherChampsSpecifiques()">
                <option value="appart">Appartement</option>
                <option value="maison">Maison</option>
                <option value="chalet">Chalet</option>
            </select>
        </div>
        
        <div class="col-md-6">
            <label class="small fw-bold text-muted mb-1">Région</label>
            <select name="id_reg" class="form-select border-0 bg-light rounded-3" required>
                <option value="">-- Choisir une région --</option>
                <option value="1">Alpes du Sud</option>
                <option value="2">Alpes du Nord</option>
                <option value="3">Pyrénées</option>
            </select>
        </div>
        <div class="col-md-6">
            <label class="small fw-bold text-muted mb-1">Station</label>
            <select name="id_station" class="form-select border-0 bg-light rounded-3" required>
                <option value="">-- Choisir une station --</option>
                <option value="1">Vars</option>
                <option value="2">Risoul</option>
                <option value="3">Serre Chevalier</option>
            </select>
        </div>

        <div class="col-md-4">
            <label class="small fw-bold text-muted mb-1">Prix / Nuit (€)</label>
            <input type="number" name="prix" class="form-control border-0 bg-light rounded-3" required>
        </div>
        <div class="col-md-4">
            <label class="small fw-bold text-muted mb-1">Voyageurs max</label>
            <input type="number" name="guests" class="form-control border-0 bg-light rounded-3">
        </div>
        <div class="col-md-4">
            <label class="small fw-bold text-muted mb-1">Nombre de lits</label>
            <input type="number" name="beds" class="form-control border-0 bg-light rounded-3">
        </div>

        <div class="col-md-4">
            <label class="small fw-bold text-muted mb-1">Surface (m²)</label>
            <input type="number" name="surface" class="form-control border-0 bg-light rounded-3">
        </div>
        <div class="col-md-4">
            <label class="small fw-bold text-muted mb-1">Nb Pièces</label>
            <input type="number" name="nb_p" class="form-control border-0 bg-light rounded-3">
        </div>
        <div class="col-md-4">
            <label class="small fw-bold text-muted mb-1">Nb Chambres</label>
            <input type="number" name="nb_c" class="form-control border-0 bg-light rounded-3">
        </div>

        <div class="col-12">
            <label class="small fw-bold text-muted mb-1">Photo principale</label>
            <input type="file" name="image_habitation" class="form-control border-0 bg-light rounded-3" accept="image/*">
        </div>
        
        <div class="col-12">
            <label class="small fw-bold text-muted mb-1">Description du logement</label>
            <textarea name="description" class="form-control border-0 bg-light rounded-3" rows="3" placeholder="Décrivez votre bien..."></textarea>
        </div>

        <div class="col-12">
            <label class="small fw-bold text-muted mb-1">Adresse</label>
            <input type="text" name="adresse" class="form-control border-0 bg-light rounded-3">
        </div>
        <div class="col-md-8">
            <input type="text" name="ville" class="form-control border-0 bg-light rounded-3" placeholder="Ville">
        </div>
        <div class="col-md-4">
            <input type="text" name="cp" class="form-control border-0 bg-light rounded-3" placeholder="CP">
        </div>

        <hr class="my-3">

        <div id="row_appart" class="col-12 bg-white p-3 rounded-3 shadow-sm mb-2">
            <h6 class="fw-bold small">Options Appartement</h6>
            <div class="d-flex gap-3 align-items-center">
                <span>Étage : <input type="number" name="etage" class="form-control d-inline-block w-25"></span>
                <label><input type="checkbox" name="ascenseur"> Ascenseur</label>
            </div>
        </div>

        <div id="row_maison" class="col-12 bg-white p-3 rounded-3 shadow-sm mb-2" style="display:none;">
            <h6 class="fw-bold small">Options Maison</h6>
            <div class="d-flex gap-3 align-items-center">
                <span>Étages : <input type="number" name="nb_etages_maison" class="form-control d-inline-block w-25"></span>
                <label><input type="checkbox" name="jardin"> Jardin</label>
            </div>
        </div>

        <div id="row_chalet" class="col-12 bg-white p-3 rounded-3 shadow-sm mb-2" style="display:none;">
            <h6 class="fw-bold small">Options Chalet</h6>
            <div class="d-flex gap-3">
                <label><input type="checkbox" name="bois"> Bois extérieur</label>
                <label><input type="checkbox" name="cheminee"> Cheminée</label>
            </div>
        </div>

        <div class="col-12">
            <label class="small fw-bold text-muted mb-2">Équipements disponibles</label>
            <div class="d-flex flex-wrap gap-2">
                <?php foreach ($lesEquipements as $unEquip) : ?>
                    <div class="form-check bg-light px-3 py-1 rounded-pill border">
                        <input class="form-check-input" type="checkbox" name="equipements[]" value="<?= $unEquip['ID_EQUIP'] ?>" id="eq_<?= $unEquip['ID_EQUIP'] ?>">
                        <label class="form-check-label small" for="eq_<?= $unEquip['ID_EQUIP'] ?>">
                            <?= htmlspecialchars($unEquip['NOM_EQUIP']) ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="col-12">
            <label class="small fw-bold text-muted mb-2">Périodes d'indisponibilité (entretien, usage personnel...)</label>
            <div id="periodes-container">
                <div class="row g-2 mb-2 periode-row">
                    <div class="col-5"><input type="date" name="periode_debut[]" class="form-control border-0 bg-light rounded-3"></div>
                    <div class="col-5"><input type="date" name="periode_fin[]" class="form-control border-0 bg-light rounded-3"></div>
                    <div class="col-2"><button type="button" class="btn btn-outline-danger rounded-3" onclick="this.closest('.periode-row').remove()">×</button></div>
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" onclick="ajouterPeriode()">+ Ajouter une période</button>
        </div>

        <script>
        function ajouterPeriode() {
            const container = document.getElementById('periodes-container');
            const row = document.createElement('div');
            row.className = 'row g-2 mb-2 periode-row';
            row.innerHTML = `
                <div class="col-5"><input type="date" name="periode_debut[]" class="form-control border-0 bg-light rounded-3"></div>
                <div class="col-5"><input type="date" name="periode_fin[]" class="form-control border-0 bg-light rounded-3"></div>
                <div class="col-2"><button type="button" class="btn btn-outline-danger rounded-3" onclick="this.closest('.periode-row').remove()">×</button></div>`;
            container.appendChild(row);
        }
        </script>

        <div class="col-12 mt-4 text-end">
            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Annuler</button>
            <button type="submit" name="ValiderBien" id="btn-valider-modal" class="btn btn-primary rounded-pill px-4" style="background-color: #4d7c6d; border:none;">Ajouter au Parc</button>
        </div>
    </div>
</form>

<script>
function afficherChampsSpecifiques() {
    const type = document.getElementById("type_bien").value;
    document.getElementById("row_appart").style.display = (type === "appart") ? "block" : "none";
    document.getElementById("row_maison").style.display = (type === "maison") ? "block" : "none";
    document.getElementById("row_chalet").style.display = (type === "chalet") ? "block" : "none";
}
</script>