@extends('layouts.app')

@section('title', 'Détails de la réservation')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="bi bi-calendar-event me-2"></i>Détails de la réservation #{{ $booking['id'] }}
                </h4>
                <span class="badge bg-{{ $booking['status'] === 'accepted' ? 'success' : ($booking['status'] === 'pending' ? 'warning' : ($booking['status'] === 'rejected' ? 'danger' : 'secondary')) }}">
                    {{ $booking['status'] }}
                </span>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6><i class="bi bi-door-open me-2"></i>Salle</h6>
                        <p class="ms-4">{{ $booking['room']['name'] ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="bi bi-geo-alt me-2"></i>Bâtiment</h6>
                        <p class="ms-4">{{ $booking['room']['building'] ?? 'N/A' }}</p>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6><i class="bi bi-clock me-2"></i>Date et heure</h6>
                        <p class="ms-4">
                            {{ \Carbon\Carbon::parse($booking['starts_at'])->format('d/m/Y H:i') }} - 
                            {{ \Carbon\Carbon::parse($booking['ends_at'])->format('H:i') }}
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="bi bi-hourglass-split me-2"></i>Durée</h6>
                        <p class="ms-4">
                            {{ \Carbon\Carbon::parse($booking['starts_at']->diffInMinutes($booking['ends_at'])) }} minutes
                        </p>
                    </div>
                </div>

                <div class="mb-4">
                    <h6><i class="bi bi-card-text me-2"></i>Objet</h6>
                    <p class="ms-4">{{ $booking['purpose'] ?? 'Non spécifié' }}</p>
                </div>

                @if(!empty($booking['equipment']))
                    <div class="mb-4">
                        <h6><i class="bi bi-tools me-2"></i>Matériel demandé</h6>
                        <div class="ms-4">
                            @foreach($booking['equipment'] as $eq)
                                <span class="badge bg-secondary me-2 mb-2">
                                    {{ $eq['name'] }} (x{{ $eq['pivot']['quantity'] ?? 1 }})
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6><i class="bi bi-person me-2"></i>Demandeur</h6>
                        <p class="ms-4">{{ $booking['user']['name'] ?? 'N/A' }}</p>
                        <p class="ms-4 small text-secondary">{{ $booking['user']['email'] ?? '' }}</p>
                    </div>
                    @if(!empty($booking['decided_by']))
                        <div class="col-md-6">
                            <h6><i class="bi bi-person-check me-2"></i>Traité par</h6>
                            <p class="ms-4">{{ $booking['decidedBy']['name'] ?? 'N/A' }}</p>
                            <p class="ms-4 small text-secondary">
                                {{ \Carbon\Carbon::parse($booking['decided_at'])->format('d/m/Y H:i') }}
                            </p>
                        </div>
                    @endif
                </div>

                @if($booking['status'] === 'rejected' && !empty($booking['rejection_reason']))
                    <div class="alert alert-danger">
                        <h6><i class="bi bi-exclamation-triangle me-2"></i>Motif du refus</h6>
                        <p class="mb-0">{{ $booking['rejection_reason'] }}</p>
                    </div>
                @endif

                <div class="mt-4">
                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Retour
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
