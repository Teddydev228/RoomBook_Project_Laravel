@extends('layouts.app')

@section('title', 'Tableau de bord - Enseignant')

@section('content')
@php
$myBookings = $bookings ?? [];
$acceptedCount = count(array_filter($myBookings, fn($b) => ($b['status'] ?? '') === 'accepted'));
$pendingCount = count(array_filter($myBookings, fn($b) => ($b['status'] ?? '') === 'pending'));
$rejectedCount = count(array_filter($myBookings, fn($b) => ($b['status'] ?? '') === 'rejected'));
$activeBookings = array_filter($myBookings, fn($b) => ($b['status'] ?? '') !== 'cancelled');
@endphp

<div class="row mb-4">
    <div class="col-12">
        <h2><i class="bi bi-grid me-2"></i>Tableau de bord - Enseignant</h2>
    </div>
</div>

<!-- Cartes de statistiques -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card stats-card p-3">
            <div class="d-flex align-items-center">
                <div class="flex-shrink-0 bg-success bg-opacity-10 p-3 rounded me-3">
                    <i class="bi bi-check-circle text-success" style="font-size: 1.5rem;"></i>
                </div>
                <div>
                    <h3 class="mb-0">{{ $acceptedCount }}</h3>
                    <p class="text-secondary mb-0">Réservations acceptées</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stats-card p-3">
            <div class="d-flex align-items-center">
                <div class="flex-shrink-0 bg-warning bg-opacity-10 p-3 rounded me-3">
                    <i class="bi bi-clock-history text-warning" style="font-size: 1.5rem;"></i>
                </div>
                <div>
                    <h3 class="mb-0">{{ $pendingCount }}</h3>
                    <p class="text-secondary mb-0">En attente</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stats-card p-3">
            <div class="d-flex align-items-center">
                <div class="flex-shrink-0 bg-danger bg-opacity-10 p-3 rounded me-3">
                    <i class="bi bi-x-circle text-danger" style="font-size: 1.5rem;"></i>
                </div>
                <div>
                    <h3 class="mb-0">{{ $rejectedCount }}</h3>
                    <p class="text-secondary mb-0">Refusées</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Planning hebdomadaire -->
    <div class="col-lg-8 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-calendar-week me-2"></i>Planning Hebdomadaire</h5>
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="prevWeek">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="nextWeek">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="calendar-container">
                    <div class="calendar-grid" id="calendarGrid"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides et réservations à venir -->
    <div class="col-lg-4 mb-4">
        <!-- Actions rapides -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-lightning me-2"></i>Actions rapides</h5>
            </div>
            <div class="card-body">
                <a href="{{ route('bookings.create') }}" class="btn btn-primary w-100 mb-2">
                    <i class="bi bi-plus-circle me-2"></i>Réserver une salle
                </a>
                <a href="{{ route('bookings.index') }}" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-list me-2"></i>Voir mes réservations
                </a>
            </div>
        </div>

        <!-- Prochaines réservations -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-upcoming me-2"></i>Prochaines réservations</h5>
                <a href="{{ route('bookings.index') }}" class="btn btn-sm btn-outline-secondary">Voir tout</a>
            </div>
            <div class="card-body p-0">
                @forelse($activeBookings as $booking)
                    @if($booking['status'] === 'accepted' && \Carbon\Carbon::parse($booking['starts_at'])->isFuture())
                        <div class="p-3 border-bottom" style="border-color: var(--border-color) !important;">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <strong>{{ $booking['room']['name'] ?? 'Salle inconnue' }}</strong>
                                <span class="badge bg-success">{{ $booking['status'] }}</span>
                            </div>
                            <div class="text-secondary small">
                                <i class="bi bi-calendar me-1"></i>
                                {{ \Carbon\Carbon::parse($booking['starts_at'])->format('d/m/Y H:i') }}
                            </div>
                            <div class="text-secondary small">
                                <i class="bi bi-geo-alt me-1"></i>
                                {{ $booking['room']['building'] ?? '' }}
                            </div>
                            @if(!empty($booking['purpose']))
                                <div class="mt-2 small text-truncate">
                                    {{ $booking['purpose'] }}
                                </div>
                            @endif
                        </div>
                    @endif
                @empty
                    <div class="p-4 text-center text-secondary">
                        <i class="bi bi-calendar-x display-6"></i>
                        <p class="mt-2 mb-0">Aucune réservation à venir</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const bookings = @json($bookings);
const rooms = @json($rooms);

document.addEventListener('DOMContentLoaded', function() {
    renderCalendar(getWeekDates(new Date()));
});

let currentWeekStart = new Date();

function getWeekDates(date) {
    const d = new Date(date);
    const day = d.getDay();
    const diff = d.getDate() - day + (day === 0 ? -6 : 1);
    return new Date(d.setDate(diff));
}

function formatDate(date) {
    return date.toISOString().split('T')[0];
}

function renderWeekCalendar(weekStart) {
    currentWeekStart = new Date(weekStart);
    renderCalendar(currentWeekStart);
}

function renderCalendar(weekStart) {
    const grid = document.getElementById('calendarGrid');
    grid.innerHTML = '';

    const days = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];
    
    days.forEach(day => {
        const header = document.createElement('div');
        header.className = 'calendar-day-header';
        header.textContent = day;
        grid.appendChild(header);
    });

    for (let i = 0; i < 7; i++) {
        const date = new Date(weekStart);
        date.setDate(weekStart.getDate() + i);
        
        const dayDiv = document.createElement('div');
        dayDiv.className = 'calendar-day';
        
        const isToday = formatDate(date) === formatDate(new Date());
        if (isToday) {
            dayDiv.classList.add('day-today');
        }
        
        dayDiv.innerHTML = `<div class="day-number mb-2">${date.getDate()}</div>`;
        
        const dayBookings = bookings.filter(b => {
            const bookingDate = formatDate(new Date(b.starts_at));
            return bookingDate === formatDate(date) && b.status === 'accepted';
        });
        
        dayBookings.forEach(booking => {
            const event = document.createElement('div');
            const startTime = new Date(booking.starts_at).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
            const endTime = new Date(booking.ends_at).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
            event.className = 'booking-event booking-accepted';
            event.title = `${booking.room?.name || 'Salle'} - ${startTime} à ${endTime}`;
            event.textContent = `${startTime} - ${booking.room?.name || 'Salle'}`;
            dayDiv.appendChild(event);
        });
        
        grid.appendChild(dayDiv);
    }
}

document.getElementById('prevWeek').addEventListener('click', function() {
    const newDate = new Date(currentWeekStart);
    newDate.setDate(newDate.getDate() - 7);
    renderWeekCalendar(newDate);
});

document.getElementById('nextWeek').addEventListener('click', function() {
    const newDate = new Date(currentWeekStart);
    newDate.setDate(newDate.getDate() + 7);
    renderWeekCalendar(newDate);
});
</script>
@endpush