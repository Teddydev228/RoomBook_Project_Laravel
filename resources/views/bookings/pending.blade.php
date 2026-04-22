@extends('layouts.app')

@section('title', 'Demandes en attente')

@section('content')
<div class="row">
    <div class="col-12">
        <h2 class="mb-4">
            <i class="bi bi-clock-history me-2"></i>Demandes en attente
            @if(!empty($pendingBookings))
                <span class="badge bg-warning">{{ count($pendingBookings) }}</span>
            @endif
        </h2>
    </div>
</div>

@if(empty($pendingBookings))
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-check-circle display-4 text-success mb-3"></i>
            <h5>Toutes les demandes ont été traitées</h5>
            <p class="text-secondary">Il n'y a aucune réservation en attente de décision.</p>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-primary mt-2">
                <i class="bi bi-arrow-left me-2"></i>Retour au tableau de bord
            </a>
        </div>
    </div>
@else
    <div class="card">
        <div class="card-header">
            <i class="bi bi-list me-2"></i>Liste des demandes en attente
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Enseignant</th>
                            <th>Salle</th>
                            <th>Date / Heure</th>
                            <th>Objet</th>
                            <th>Matériel</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingBookings as $booking)
                            <tr>
                                <td>#{{ $booking['id'] }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar me-2">
                                            {{ strtoupper(substr($booking['user']['name'] ?? 'U', 0, 1)) }}
                                        </div>
                                        <div>
                                            <strong>{{ $booking['user']['name'] ?? 'N/A' }}</strong><br>
                                            <small class="text-secondary">{{ $booking['user']['email'] ?? '' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <strong>{{ $booking['room']['name'] ?? 'N/A' }}</strong><br>
                                    <small class="text-secondary">
                                        <i class="bi bi-geo-alt me-1"></i>{{ $booking['room']['building'] ?? '' }}
                                        ({{ $booking['room']['capacity'] ?? '?' }} pers.)
                                    </small>
                                </td>
                                <td>
                                    <div>{{ \Carbon\Carbon::parse($booking['starts_at'])->format('d/m/Y') }}</div>
                                    <small class="text-secondary">
                                        {{ \Carbon\Carbon::parse($booking['starts_at'])->format('H:i') }} 
                                        - {{ \Carbon\Carbon::parse($booking['ends_at'])->format('H:i') }}
                                    </small>
                                </td>
                                <td>
                                    <p style="max-width: 250px;">{{ $booking['purpose'] ?? '' }}</p>
                                </td>
                                <td>
                                    @if(!empty($booking['equipment']))
                                        @foreach($booking['equipment'] as $eq)
                                            <span class="badge bg-secondary me-1">
                                                {{ $eq['name'] }} x{{ $eq['pivot']['quantity'] ?? 1 }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="text-secondary">Aucun</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" 
                                                class="btn btn-sm btn-success"
                                                data-bs-toggle="modal"
                                                data-bs-target="#decisionModal"
                                                data-booking-id="{{ $booking['id'] }}"
                                                data-decision="accepted">
                                            <i class="bi bi-check-lg"></i> Accepter
                                        </button>
                                        <button type="button" 
                                                class="btn btn-sm btn-danger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#decisionModal"
                                                data-booking-id="{{ $booking['id'] }}"
                                                data-decision="rejected">
                                            <i class="bi bi-x-lg"></i> Refuser
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif

    <!-- Decision Modal -->
    <div class="modal fade" id="decisionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="decisionForm" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Confirmer la décision</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="status" id="decisionStatus">
                        <div id="rejectionReasonGroup" class="mb-3" style="display: none;">
                            <label class="form-label">Motif du refus <span class="text-danger">*</span></label>
                            <textarea name="rejection_reason" 
                                      class="form-control" 
                                      rows="3" 
                                      required
                                      placeholder="Expliquez pourquoi cette réservation est refusée..."></textarea>
                        </div>
                        <p id="confirmMessage" class="mb-0"></p>
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

@push('scripts')
<script>
const baseUrl = "{{ url('/') }}";

// Decision modal setup
const decisionModal = document.getElementById('decisionModal');
if (decisionModal) {
    decisionModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const bookingId = button.getAttribute('data-booking-id');
        const decision = button.getAttribute('data-decision');
        
        const modalTitle = decisionModal.querySelector('.modal-title');
        const form = decisionModal.querySelector('#decisionForm');
        const statusInput = decisionModal.querySelector('#decisionStatus');
        const reasonGroup = decisionModal.querySelector('#rejectionReasonGroup');
        const reasonField = decisionModal.querySelector('textarea[name="rejection_reason"]');
        const confirmBtn = decisionModal.querySelector('#confirmDecisionBtn');
        const confirmMessage = decisionModal.querySelector('#confirmMessage');
        
        statusInput.value = decision;
        form.action = baseUrl + "/bookings/" + bookingId + "/decision";
        
        if (decision === 'rejected') {
            modalTitle.textContent = 'Refuser la réservation';
            reasonGroup.style.display = 'block';
            reasonField.required = true;
            confirmBtn.className = 'btn btn-danger';
            confirmMessage.textContent = 'Êtes-vous sûr de vouloir refuser cette réservation ?';
        } else {
            modalTitle.textContent = 'Accepter la réservation';
            reasonGroup.style.display = 'none';
            reasonField.required = false;
            reasonField.value = '';
            confirmBtn.className = 'btn btn-success';
            confirmMessage.textContent = 'Êtes-vous sûr de vouloir accepter cette réservation ?';
        }
    });
}

// Form submission
const decisionForm = document.getElementById('decisionForm');
if (decisionForm) {
    decisionForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const apiToken = '{{ session("api_token") }}';
        
        fetch(this.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Authorization': 'Bearer ' + apiToken
            },
            body: JSON.stringify(data)
        })
        .then(async r => {
            const payload = await r.json().catch(() => ({}));
            if (!r.ok) {
                throw new Error(payload.message || 'Erreur serveur');
            }
            return payload;
        })
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert(data.message || 'Erreur');
            }
        })
        .catch(err => {
            alert(err.message || 'Erreur réseau');
        });
    });
}
</script>
@endpush
