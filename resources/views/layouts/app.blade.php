<!DOCTYPE html>
<html lang="fr" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'RoomBook') - Gestion de Salles</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <style>
        :root {
            --bg-primary: #0d1117;
            --bg-secondary: #161b22;
            --bg-tertiary: #21262d;
            --border-color: #30363d;
            --text-primary: #f0f6fc;
            --text-secondary: #8b949e;
            --primary: #238636;
            --primary-hover: #2ea043;
        }
        
        body {
            background-color: var(--bg-primary);
            color: var(--text-primary);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        }
        
        .navbar {
            background-color: var(--bg-secondary) !important;
            border-bottom: 1px solid var(--border-color);
        }
        
        .navbar-brand {
            font-weight: 600;
            color: var(--text-primary) !important;
        }
        
        .nav-link {
            color: var(--text-secondary) !important;
            padding: 0.75rem 1rem;
            border-radius: 6px;
            margin: 2px 8px;
            transition: all 0.2s;
        }
        
        .nav-link:hover, .nav-link.active {
            background-color: var(--bg-tertiary);
            color: var(--text-primary) !important;
        }
        
        .nav-link i {
            margin-right: 8px;
            font-size: 1.1rem;
        }
        
        .card {
            background-color: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 8px;
        }
        
        .card-header {
            background-color: var(--bg-tertiary);
            border-bottom: 1px solid var(--border-color);
            color: var(--text-primary);
        }
        
        .table {
            color: var(--text-primary);
        }
        
        .table th {
            background-color: var(--bg-tertiary);
            color: var(--text-secondary);
            border-color: var(--border-color);
        }
        
        .table td {
            border-color: var(--border-color);
        }
        
        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }
        
        .btn-primary:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
        }
        
        .btn-danger {
            background-color: #da3633;
            border-color: #da3633;
        }
        
        .btn-outline-secondary {
            --bs-btn-color: var(--text-secondary);
            --bs-btn-border-color: var(--border-color);
            --bs-btn-hover-color: var(--text-primary);
            --bs-btn-hover-bg: var(--bg-tertiary);
            --bs-btn-hover-border-color: var(--border-color);
        }
        
        .form-control, .form-select {
            background-color: var(--bg-primary);
            border-color: var(--border-color);
            color: var(--text-primary);
        }
        
        .form-control:focus, .form-select:focus {
            background-color: var(--bg-primary);
            border-color: #58a6ff;
            color: var(--text-primary);
            box-shadow: 0 0 0 2px rgba(88, 166, 255, 0.2);
        }
        
        .form-floating label {
            color: var(--text-secondary);
        }
        
        .badge.bg-success { background-color: var(--primary) !important; }
        .badge.bg-warning { background-color: #d29922 !important; color: #000; }
        .badge.bg-danger { background-color: #f85149 !important; }
        .badge.bg-info { background-color: #58a6ff !important; }
        .badge.bg-secondary { background-color: var(--text-secondary) !important; }
        
        .main-content {
            padding: 2rem;
        }
        
        .stats-card {
            transition: transform 0.2s;
        }
        
        .stats-card:hover {
            transform: translateY(-4px);
        }
        
        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3d6148 0%, #4a7a5c 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.85rem;
            color: white;
        }
        
        .dropdown-menu {
            background-color: var(--bg-secondary);
            border-color: var(--border-color);
        }
        
        .dropdown-item {
            color: var(--text-primary);
        }
        
        .dropdown-item:hover {
            background-color: var(--bg-tertiary);
        }
        
        .alert-success {
            background-color: rgba(35, 134, 54, 0.15);
            color: #3fb950;
        }
        
        .alert-danger {
            background-color: rgba(248, 81, 73, 0.15);
            color: #f85149;
        }
        
        .modal-content {
            background-color: var(--bg-secondary);
            border: 1px solid var(--border-color);
        }
        
        .modal-header, .modal-footer {
            border-color: var(--border-color);
        }
        
        .btn-close {
            filter: invert(1);
        }
        
        /* Calendar styles */
        .calendar-container {
            background-color: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            overflow: hidden;
        }
        
        .calendar-header {
            background-color: var(--bg-tertiary);
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
        }
        
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            min-height: 500px;
        }
        
        .calendar-day-header {
            background-color: var(--bg-tertiary);
            padding: 0.75rem;
            text-align: center;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-secondary);
            font-weight: 600;
        }
        
        .calendar-day {
            min-height: 120px;
            border-right: 1px solid var(--border-color);
            padding: 4px;
            background-color: var(--bg-primary);
        }
        
        .calendar-day:nth-child(7n) {
            border-right: none;
        }
        
        .day-number {
            font-size: 0.85rem;
            color: var(--text-secondary);
            margin-bottom: 4px;
        }
        
        .day-today .day-number {
            background-color: var(--primary);
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .booking-event {
            font-size: 0.75rem;
            padding: 2px 6px;
            border-radius: 4px;
            margin-bottom: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            cursor: pointer;
        }
        
        .booking-pending { background-color: #d29922; color: #000; }
        .booking-accepted { background-color: #238636; color: white; }
        .booking-rejected { background-color: #f85149; color: white; }
        .booking-cancelled { background-color: #6e7681; color: white; }
        
        /* Role badges */
        .role-badge {
            text-transform: capitalize;
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
        
        .page-header {
            margin-bottom: 1.5rem;
        }
        
        .page-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }
        
        .page-subtitle {
            font-size: 0.9rem;
            color: var(--text-secondary);
        }
        
        .actions-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        
        footer {
            text-align: center;
            color: var(--text-secondary);
            font-size: 0.85rem;
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
        }
        
        .stat-label {
            font-size: 0.85rem;
            color: var(--text-secondary);
        }
    </style>
    
    @stack('styles')
</head>
<body>
    @php($currentUser = $user ?? session('user'))
    @php($roleDashboard = match($currentUser['role'] ?? null) {
        'admin' => route('admin.dashboard'),
        'responsable' => route('responsable.dashboard'),
        'enseignant' => route('enseignant.dashboard'),
        default => route('login'),
    })

    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ $roleDashboard }}">
                <i class="bi bi-building me-2"></i>RoomBook
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    @if($currentUser)
                        @if($currentUser['role'] === 'enseignant')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('enseignant.dashboard') }}">
                                    <i class="bi bi-grid"></i>Enseignant
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('bookings.create') }}">
                                    <i class="bi bi-plus-circle"></i>Réserver
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('bookings.index') }}">
                                    <i class="bi bi-list"></i>Mes Réservations
                                </a>
                            </li>
                        @elseif($currentUser['role'] === 'responsable')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('responsable.dashboard') }}">
                                    <i class="bi bi-grid"></i>Responsable
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('bookings.pending') }}">
                                    <i class="bi bi-clock-history"></i>En attente
                                    @isset($pendingCount)
                                        @if($pendingCount > 0)
                                            <span class="badge bg-danger ms-1">{{ $pendingCount }}</span>
                                        @endif
                                    @endisset
                                </a>
                            </li>
                        @elseif($currentUser['role'] === 'admin')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                    <i class="bi bi-grid"></i>Admin
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('users.index') }}">
                                    <i class="bi bi-people"></i>Utilisateurs
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('rooms.index') }}">
                                    <i class="bi bi-door-open"></i>Salles
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('equipment.index') }}">
                                    <i class="bi bi-tools"></i>Matériel
                                </a>
                            </li>
                        @endif
                    @endif
                </ul>
                
                <ul class="navbar-nav">
                    @if($currentUser)
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                                <div class="user-avatar me-2">
                                    {{ strtoupper(substr($currentUser['name'], 0, 1)) }}
                                </div>
                                <span class="d-none d-md-inline">{{ $currentUser['name'] }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark">
                                <li><span class="dropdown-item-text text-secondary small">{{ $currentUser['email'] }}</span></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-box-arrow-right me-2"></i>Déconnexion
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="btn btn-outline-primary btn-sm" href="{{ route('login') }}">
                                <i class="bi bi-box-arrow-in-right me-1"></i>Connexion
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    <main class="main-content" style="margin-top: 56px;">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="text-center py-4 text-secondary" style="font-size: 0.85rem;">
        <div class="container">
            <hr style="border-color: var(--border-color);">
            <p class="mb-0">© 2026 RoomBook - Système de Gestion de Salles</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>