@extends('layouts.app')

@section('title', 'Administration')

@section('content')
@php
$pendingCount = count(array_filter($allBookings ?? [], fn($b) => ($b['status'] ?? '') === 'pending'));
@endphp

<div class="row mb-4">
    <div class="col-12">
        <h2><i class="bi bi-speedometer2 me-2"></i>Administration</h2>
    </div>
</div>

<!-- Statistiques Admin -->
<div class="row mb-4">
    <div class="col-md-3">
        <a href="{{ route('users.index') }}" class="text-decoration-none">
            <div class="card stats-card p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-primary bg-opacity-10 p-3 rounded me-3">
                        <i class="bi bi-people text-primary" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h3 class="mb-0">{{ count($users ?? []) }}</h3>
                        <p class="text-secondary mb-0 small">Utilisateurs</p>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('rooms.index') }}" class="text-decoration-none">
            <div class="card stats-card p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-success bg-opacity-10 p-3 rounded me-3">
                        <i class="bi bi-door-open text-success" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h3 class="mb-0">{{ count($rooms ?? []) }}</h3>
                        <p class="text-secondary mb-0 small">Salles</p>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('equipment.index') }}" class="text-decoration-none">
            <div class="card stats-card p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-info bg-opacity-10 p-3 rounded me-3">
                        <i class="bi bi-tools text-info" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h3 class="mb-0">{{ count($equipment ?? []) }}</h3>
                        <p class="text-secondary mb-0 small">Matériels</p>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('bookings.pending') }}" class="text-decoration-none">
            <div class="card stats-card p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-warning bg-opacity-10 p-3 rounded me-3">
                        <i class="bi bi-clock-history text-warning" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h3 class="mb-0">{{ $pendingCount }}</h3>
                        <p class="text-secondary mb-0 small">En attente</p>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

<div class="row g-3">
    <!-- Gestion Utilisateurs -->
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-people me-2"></i>Utilisateurs</h5>
                <a href="{{ route('users.create') }}" class="btn btn-sm btn-primary">Nouveau</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <tbody>
                            @forelse(array_slice($users ?? [], 0, 4) as $user)
                                <tr>
                                    <td>
                                        <div class="user-avatar me-2" style="width: 28px; height: 28px; font-size: 0.7rem;">
                                            {{ strtoupper(substr($user['name'] ?? 'U', 0, 1)) }}
                                        </div>
                                        {{ $user['name'] }}
                                    </td>
                                    <td class="small text-secondary">{{ $user['email'] }}</td>
                                    <td>
                                        <span class="badge bg-{{ $user['role'] === 'admin' ? 'danger' : ($user['role'] === 'responsable' ? 'warning' : 'info') }}">
                                            {{ $user['role'] }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-secondary">Aucun utilisateur</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Gestion Salles -->
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-door-open me-2"></i>Salles</h5>
                <a href="{{ route('rooms.create') }}" class="btn btn-sm btn-success">Nouvelle</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <tbody>
                            @forelse(array_slice($rooms ?? [], 0, 4) as $room)
                                <tr>
                                    <td>{{ $room['name'] }}</td>
                                    <td class="small text-secondary">{{ $room['building'] }}</td>
                                    <td>{{ $room['capacity'] }}</td>
                                    <td>
                                        @if(($room['is_available'] ?? true))
                                            <span class="badge bg-success">Oui</span>
                                        @else
                                            <span class="badge bg-danger">Non</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-secondary">Aucune salle</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mt-3">
    <!-- Gestion Matériels -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-tools me-2"></i>Matériels</h5>
                <a href="{{ route('equipment.create') }}" class="btn btn-sm btn-info">Nouveau</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <tbody>
                            @forelse(array_slice($equipment ?? [], 0, 4) as $eq)
                                <tr>
                                    <td>{{ $eq['name'] }}</td>
                                    <td>{{ $eq['quantity'] }}</td>
                                    <td>
                                        @if(($eq['is_available'] ?? true))
                                            <span class="badge bg-success">Dispo</span>
                                        @else
                                            <span class="badge bg-danger">Indispo</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-secondary">Aucun équipement</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Dernières Réservations -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-calendar-check me-2"></i>Réservations</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <tbody>
                            @forelse(array_slice($allBookings ?? [], 0, 4) as $booking)
                                <tr>
                                    <td>
                                        <strong>{{ $booking['room']['name'] ?? '-' }}</strong>
                                    </td>
                                    <td class="small">
                                        {{ \Carbon\Carbon::parse($booking['starts_at'] ?? now())->format('d/m H:i') }}
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $booking['status'] === 'accepted' ? 'success' : ($booking['status'] === 'rejected' ? 'danger' : 'warning') }}">
                                            {{ $booking['status'] }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-secondary">Aucune réservation</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection