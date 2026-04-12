<!---
Titre       : Test CI/CD - Lycée Paul-Louis Courier
Auteur      : Louis MEDO
Date        : 03/04/2026
Rôle        : Vérification du bon fonctionnement des CDN et des ressources externes après déploiement CI/CD
--->

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test CDN</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=JetBrains+Mono&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; background: #f8f9fa; }
        .mono { font-family: 'JetBrains Mono', monospace; }
        .cdn-row { transition: background .15s; }
        .cdn-row:hover { background: #f1f3f5; }
        .status-badge { min-width: 90px; }
    </style>
</head>
<body class="py-5">
<div class="container" style="max-width:700px">

    <div class="text-center mb-5">
        <h1 class="fw-semibold">Test CDN <i class="bi bi-cloud-check text-primary"></i></h1>
        <p class="text-muted mono"><?= date('d/m/Y H:i:s') ?> · PHP <?= PHP_VERSION ?></p>
    </div>

    <!-- Tableau des CDN -->
    <div class="card shadow-sm mb-4">
        <div class="card-header fw-semibold">Ressources externes chargées</div>
        <table class="table table-borderless mb-0">
            <thead class="table-light">
                <tr>
                    <th>Service</th>
                    <th>Ressource</th>
                    <th class="text-center">Statut</th>
                </tr>
            </thead>
            <tbody>
                <tr class="cdn-row"><td>Google Fonts</td><td class="mono text-muted" style="font-size:.8rem">fonts.googleapis.com</td><td class="text-center"><span id="s-fonts" class="badge status-badge bg-secondary">...</span></td></tr>
                <tr class="cdn-row"><td>Bootstrap CSS</td><td class="mono text-muted" style="font-size:.8rem">cdn.jsdelivr.net</td><td class="text-center"><span id="s-bs-css" class="badge status-badge bg-secondary">...</span></td></tr>
                <tr class="cdn-row"><td>Bootstrap JS</td><td class="mono text-muted" style="font-size:.8rem">cdn.jsdelivr.net</td><td class="text-center"><span id="s-bs-js" class="badge status-badge bg-secondary">...</span></td></tr>
                <tr class="cdn-row"><td>Bootstrap Icons</td><td class="mono text-muted" style="font-size:.8rem">cdn.jsdelivr.net</td><td class="text-center"><span id="s-icons" class="badge status-badge bg-secondary">...</span></td></tr>
            </tbody>
        </table>
    </div>

    <!-- Démo Bootstrap -->
    <div class="card shadow-sm mb-4">
        <div class="card-header fw-semibold">Démo composants Bootstrap</div>
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2 mb-3">
                <button class="btn btn-primary"><i class="bi bi-check-circle me-1"></i>Primary</button>
                <button class="btn btn-success"><i class="bi bi-shield-check me-1"></i>Success</button>
                <button class="btn btn-outline-secondary">Outline</button>
            </div>
            <div class="alert alert-success py-2 mb-3">
                <i class="bi bi-fonts me-2"></i>Google Fonts <strong>Inter</strong> + <strong class="mono">JetBrains Mono</strong> actives si ce texte est bien typographié.
            </div>
            <div class="progress" style="height:8px">
                <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated" style="width:75%"></div>
            </div>
        </div>
    </div>

    <p class="text-center text-muted mono" style="font-size:.75rem">
        Serveur : <?= htmlspecialchars($_SERVER['SERVER_SOFTWARE'] ?? 'Apache') ?>
    </p>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
function checkLoaded(id, testFn) {
    const el = document.getElementById(id);
    const ok = testFn();
    el.textContent = ok ? '✓ OK' : '✗ KO';
    el.className = 'badge status-badge ' + (ok ? 'bg-success' : 'bg-danger');
}

window.addEventListener('load', () => {
    document.fonts.ready.then(() => {
        const loaded = [...document.fonts].some(f => f.family.includes('Inter') && f.status === 'loaded');
        checkLoaded('s-fonts', () => loaded);
    });

    checkLoaded('s-bs-css', () => {
        const el = document.createElement('div');
        el.className = 'd-none'; document.body.appendChild(el);
        const ok = getComputedStyle(el).display === 'none';
        el.remove(); return ok;
    });

    checkLoaded('s-bs-js', () => typeof window.bootstrap !== 'undefined');

    checkLoaded('s-icons', () => {
        const el = document.createElement('i');
        el.className = 'bi bi-check';
        el.style.cssText = 'position:absolute;visibility:hidden';
        document.body.appendChild(el);
        const ok = getComputedStyle(el).fontFamily.includes('bootstrap-icons');
        el.remove(); return ok;
    });
});
</script>
</body>
</html>