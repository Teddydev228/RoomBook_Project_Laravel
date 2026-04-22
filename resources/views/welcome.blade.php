<!DOCTYPE html>
<html lang="fr" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RoomBook - Gestion de Salles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --bg-primary: #0d1117;
            --text-primary: #f0f6fc;
            --primary: #238636;
        }
        body {
            background-color: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        .hero-section {
            text-align: center;
            padding: 4rem 2rem;
        }
        .logo {
            font-size: 4rem;
            color: var(--primary);
            margin-bottom: 1rem;
        }
        .tagline {
            font-size: 1.5rem;
            color: #8b949e;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="hero-section">
            <div class="logo">
                <i class="bi bi-building"></i>
            </div>
            <h1 class="display-3 fw-bold mb-3">RoomBook</h1>
            <p class="tagline">Système de Réservation de Salles et Matériels</p>
            
            <div class="d-flex gap-3 justify-content-center">
                @if(Route::has('login'))
                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg px-5">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Connexion
                    </a>
                @endif
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-lg px-5">
                    <i class="bi bi-grid me-2"></i>Tableau de bord
                </a>
            </div>
            
            <div class="mt-5">
                <small class="text-secondary">
                    <i class="bi bi-shield-check me-1"></i>Accès réservé aux utilisateurs autorisés
                </small>
            </div>
        </div>
    </div>
</body>
</html>