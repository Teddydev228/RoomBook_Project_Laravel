@extends('layouts.app')

@section('title', 'Tableau de bord - Responsable')

@section('content')
@php
$pendingCount = count($pendingBookings ?? []);
$acceptedCount = count(array_filter($allBookings ?? [], fn($b) => ($b['status'] ?? '') === 'accepted'));
$rejectedCount = count(array_filter($allBookings ?? [], fn($b) => ($b['status'] ?? '') === 'rejected'));
@endphp

<div class="row mb-4">
    <div class="col-12">
        <h2><i class="bi bi-grid me-2"></i>Tableau de bord - Responsable</h2>
    </div>
</div>

<!-- Statistiques -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card stats-card p-3">
            <div class="d-flex align-items-center">
                <div class="flex-shrink-0 bg-warning bg-opacity-10 p-3 rounded me-3">
                    <i class="bi bi-clock-history text-warning" style="font-size: 1.5rem;"></i>
                </div>
                <div>
                    <h3 class="mb-0">{{ $pendingCount }}</h3>
                    <p class="text-secondary mb-0">Demandes en attente</p>
                </div>
            </div>
        </div>
    </div>
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
                <div class="flex-shrink-0 bg-danger bg-opacity-10 p-3 rounded me-3">
                    <i class="bi bi-x-circle text-danger" style="font-size: 1.5rem;"></i>
                </div>
                <div>
                    <h3 class="mb-0">{{ $rejectedCount }}</h3>
                    <p class="text-secondary mb-0">Réservations refusées</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-door-open me-2"></i>Salles disponibles</h5>
                <span class="badge bg-success">{{ count(array_filter($rooms ?? [], fn($r) => ($r['is_available'] ?? false) === true)) }}</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Bâtiment</th>
                                <th>Capacité</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(array_filter($rooms ?? [], fn($r) => ($r['is_available'] ?? false) === true) as $room)
                                <tr>
                                    <td>{{ $room['name'] }}</td>
                                    <td>{{ $room['building'] }}</td>
                                    <td>{{ $room['capacity'] }} pers.</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-tools me-2"></i>Matériels</h5>
                <span class="badge bg-info">{{ count($equipment ?? []) }}</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Quantité</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($equipment ?? [] as $eq)
                                <tr>
                                    <td>{{ $eq['name'] }}</td>
                                    <td><strong>{{ $eq['quantity'] }}</strong></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-clock-history me-2"></i>Demandes en attente de traitement
                </h5>
                <span class="badge bg-warning">{{ $pendingCount }}</span>
            </div>
            <div class="card-body">
                @if(empty($pendingBookings))
                    <div class="text-center py-5 text-secondary">
                        <i class="bi bi-check-circle display-4 mb-3"></i>
                        <p>Toutes les demandes ont été traitées.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Enseignant</th>
                                    <th>Salle</th>
                                    <th>Début</th>
                                    <th>Fin</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingBookings as $booking)
                                    <tr>
                                        <td>#{{ $booking['id'] }}</td>
                                        <td>
                                            <div>{{ $booking['user']['name'] ?? 'N/A' }}</div>
                                            <small class="text-secondary">{{ $booking['user']['email'] ?? '' }}</small>
                                        </td>
                                        <td>
                                            {{ $booking['room']['name'] ?? 'N/A' }}<br>
                                            <small class="text-secondary">{{ $booking['room']['building'] ?? '' }}</small>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($booking['starts_at'])->format('d/m/Y H:i') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($booking['ends_at'])->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button"
                                                        class="btn btn-sm btn-success"
                                                        data-booking-id="{{ $booking['id'] }}"
                                                        data-decision-status="accepted">
                                                    <i class="bi bi-check-lg"></i>
                                                </button>
                                                <button type="button"
                                                        class="btn btn-sm btn-danger"
                                                        data-booking-id="{{ $booking['id'] }}"
                                                        data-decision-status="rejected">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-calendar-week me-2"></i>Aperçu du planning global
                </h5>
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
</div>

<div class="modal fade" id="decisionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="decisionForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Décision sur la réservation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="status" id="decisionStatus">
                    <div id="rejectionReasonGroup" class="mb-3" style="display: none;">
                        <label class="form-label">Motif du refus</label>
                        <textarea name="rejection_reason" class="form-control" rows="3"
                                  placeholder="Expliquez pourquoi cette réservation est refusée..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn" id="confirmDecisionBtn">Confirmer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

<script id="managerBookingsData" type="application/json">{{ json_encode($allBookings ?? []) }}</script>

@push('scripts')
<script>
const allBookings = JSON.parse(document.getElementById('managerBookingsData')?.textContent || '[]');
let currentWeekStart = getWeekDates(new Date());

document.addEventListener('DOMContentLoaded', function() {
    renderCalendar(currentWeekStart);
});

function getWeekDates(date) {
    const d = new Date(date);
    const day = d.getDay();
    const diff = d.getDate() - day + (day === 0 ? -6 : 1);
    return new Date(d.setDate(diff));
}

function formatDate(date) {
    return date.toISOString().split('T')[0];
}

function renderCalendar(weekStart) {
    currentWeekStart = new Date(weekStart);
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

        const dayBookings = (allBookings || []).filter(b => {
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
    renderCalendar(newDate);
});

document.getElementById('nextWeek').addEventListener('click', function() {
    const newDate = new Date(currentWeekStart);
    newDate.setDate(newDate.getDate() + 7);
    renderCalendar(newDate);
});

document.querySelectorAll('[data-booking-id][data-decision-status]').forEach(button => {
    button.addEventListener('click', function () {
        const bookingId = this.dataset.bookingId;
        const status = this.dataset.decisionStatus;
        decisionModal(bookingId, status);
    });
});

function decisionModal(bookingId, status) {
    const modal = new bootstrap.Modal(document.getElementById('decisionModal'));
    const form = document.getElementById('decisionForm');
    const statusInput = document.getElementById('decisionStatus');
    const reasonGroup = document.getElementById('rejectionReasonGroup');
    const confirmBtn = document.getElementById('confirmDecisionBtn');

    statusInput.value = status;
    form.action = `/bookings/${bookingId}/decision`;

    if (status === 'rejected') {
        reasonGroup.style.display = 'block';
        confirmBtn.className = 'btn btn-danger';
    } else {
        reasonGroup.style.display = 'none';
        confirmBtn.className = 'btn btn-success';
    }

    modal.show();
}

document.getElementById('decisionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());

    fetch(this.action, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
        },
        body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(data => {
        if (data.message) {
            alert(data.message);
            location.reload();
        }
    })
    .catch(() => {
        alert('Erreur lors de la décision');
    });
});
</script>
@endpush