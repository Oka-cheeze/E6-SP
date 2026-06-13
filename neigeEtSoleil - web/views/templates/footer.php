</main>
<footer class="bg-dark text-white pt-5 pb-4">
    <div class="container text-center text-md-left">
        <div class="row text-center text-md-left">
            
            <div class="col-md-4 col-lg-4 col-xl-4 mx-auto mt-3 text-start">
                <h5 class="text-uppercase mb-4 font-weight-bold text-primary">Neige et Soleil</h5>
                <p class="small text-muted">
                    Votre partenaire de confiance pour des vacances inoubliables en montagne. 
                    Nous sélectionnons les meilleurs chalets et appartements pour votre confort.
                </p>
            </div>

            <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mt-3 text-start">
                <h6 class="text-uppercase mb-4 font-weight-bold">Aide</h6>
                <p><a href="#" class="text-white-50 text-decoration-none small">Comment réserver ?</a></p>
                <p><a href="index.php?action=mentions_legales" class="text-white-50 text-decoration-none small">Mentions légales</a></p>
            </div>

            <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mt-3 text-start">
                <h6 class="text-uppercase mb-4 font-weight-bold">Contact</h6>
                <p class="small text-white-50">123 Rue des Sommets, 05120 Saint-Véran</p>
                <p class="small text-white-50">contact@neigeetsoleil.fr</p>
                <p class="small text-white-50">+33 4 00 00 00 00</p>
            </div>
        </div>

        <hr class="mb-4">

        <div class="row align-items-center">
            <div class="col-md-12 text-center">
                <p class="small text-white-50">
                    © 2026 Copyright : <strong>Neige et Soleil</strong>
                </p>
            </div>
        </div>
    </div>
    
    <!-- CORRECTION BUG 5 : Bouton panier flottant supprimé (déjà présent dans le header) -->
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var toastElList = [].slice.call(document.querySelectorAll('.toast'))
        var toastList = toastElList.map(function (toastEl) {
            return new bootstrap.Toast(toastEl, { delay: 3000 });
        });
        
        // Si on veut forcer la fermeture après le délai
        setTimeout(function() {
            toastElList.forEach(t => t.classList.remove('show'));
        }, 3000);
    });
</script>
</body>
</html>