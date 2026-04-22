@extends('layouts.app')

@section('title', 'Mes Réservations')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>
                <i class="bi bi-list me-2"></i>Mes Réservations
            </h2>
            <a href="{{ route('bookings.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Nouvelle Réservation
            </a>
        </div>

        @if(empty($bookings))
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-calendar-x display-4 text-secondary mb-3"></i>
                    <h5 class="text-secondary">Aucune réservation</h5>
                    <p class="text-secondary">Vous n'avez pas encore fait de réservation.</p>
                    <a href="{{ route('bookings.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Faire une réservation
                    </a>
                </div>
            </div>
        @else
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Salle</th>
                                    <th>Date / Heure</th>
                                    <th>Objet</th>
                                    <th>Matériel</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bookings as $booking)
                                    <tr>
                                        <td>#{{ $booking['id'] }}</td>
                                        <td>
                                            <strong>{{ $booking['room']['name'] ?? 'N/A' }}</strong><br>
                                            <small class="text-secondary">{{ $booking['room']['building'] ?? '' }}</small>
                                        </td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($booking['starts_at'])->format('d/m/Y') }}<br>
                                            <small>{{ \Carbon\Carbon::parse($booking['starts_at'])->format('H:i') }} - {{ \Carbon\Carbon::parse($booking['ends_at'])->format('H:i') }}</small>
                                        </td>
                                        <td>
                                            <div style="max-width: 250px;">
                                                <small>{{ \Illuminate\Support\Str::limit($booking['purpose'] ?? '', 100) }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            @if(!empty($booking['equipment']))
                                                @foreach($booking['equipment'] as $eq)
                                                    <span class="badge bg-secondary me-1 mb-1">
                                                        {{ $eq['name'] }} x{{ $eq['pivot']['quantity'] ?? 1 }}
                                                    </span>
                                                @endforeach
                                            @else
                                                <span class="text-secondary">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $statusClass = [
                                                    'pending' => 'bg-warning',
                                                    'accepted' => 'bg-success',
                                                    'rejected' => 'bg-danger',
                                                    'cancelled' => 'bg-secondary',
                                                ][$booking['status']] ?? 'bg-secondary';
                                            @endphp
                                            <span class="badge {{ $statusClass }}">
                                                {{ $booking['status'] }}
                                            </span>
                                            @if($booking['status'] === 'rejected' && !empty($booking['rejection_reason']))
                                                <div class="mt-1">
                                                    <small class="text-danger" data-bs-toggle="tooltip" title="{{ $booking['rejection_reason'] }}">
                                                        <i class="bi bi-info-circle"></i> Raison
                                                    </small>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            @if($booking['status'] === 'accepted')
                                                <form method="POST" action="{{ route('bookings.cancel', $booking['id']) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-outline-danger"
                                                            onclick="return confirm('Annuler cette réservation ?')">
                                                        <i class="bi bi-x-circle"></i> Annuler
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-secondary">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
